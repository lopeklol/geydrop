document.querySelectorAll(".case_count_button").forEach(button => {
    button.addEventListener("click", function() {
        let caseCount = this.getAttribute("data-count");
        document.querySelector("#case_count_input").value = caseCount;
        document.querySelector("#case_count_form").submit();
    });
});

if (document.querySelector("#live_drop_contents")) {
    setTimeout(() => {
        window.location.reload();
    }, 10000);
}

const open_case_button = document.querySelector("#case_open");
const open_case_ad_button = document.querySelector("#case_open_ab");

if (open_case_button) {
    open_case_button.addEventListener("click", () => {
        open_case_button.setAttribute("disabled", "");
        if (open_case_ad_button) { open_case_ad_button.setAttribute("disabled", "") };
        const urlParams = new URLSearchParams(window.location.search);
        let case_name_to_send;
        if (urlParams.get("case") === null) {
            case_name_to_send = window.location.pathname.split('/').pop();
        } else {
            case_name_to_send = urlParams.get("case");
        }
        let dataToSend = {
            case_name: case_name_to_send,
            case_count: parseInt(document.querySelector("#case_count_input").value),
            ad: false,
        };
        console.log(dataToSend);
        
        open_case(dataToSend);
    });
}

if (open_case_ad_button) {
    open_case_ad_button.addEventListener("click", async () => {
        if (open_case_button) { open_case_button.setAttribute("disabled", "") };
        open_case_ad_button.setAttribute("disabled", "");
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get("case") === null) {
            case_name_to_send = window.location.pathname.split('/').pop();
        } else {
            case_name_to_send = urlParams.get("case");
        }
        let dataToSend = {
            case_name: case_name_to_send,
            case_count: parseInt(document.querySelector("#case_count_input").value),
            ad: true,
        };
        
        startAd(dataToSend);
    });

    async function startAd(dataToSend) {
        fetch("https://lolocalhost/keydrop/ab_engine/start_ab.php")
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                const adToken = data.token;
                fetch("https://localhost/keydrop/ab_engine/get_ab.php")
                    .then(response => response.json())
                    .then(data => {
                        if (data.file) {
                            let video = document.getElementById("abVideo");
                            video.src = '/keydrop/ab_engine/' + data.file;
                            document.getElementById("abContainer").style.display = "block";

                            video.play();
                            video.controls = false;
                            video.autoplay = true;
                            video.onended = function() {
                                document.getElementById("abContainer").style.display = "none";
                                dataToSend.ad_token = adToken;
                                console.log(dataToSend);
                                setTimeout(open_case(dataToSend), 1000);
                            };
                        }
                    });
            }
        });
    };
}

if (open_case_button || open_case_ad_button) {
    function open_case(startData) {
        fetch("https://localhost/keydrop/open_case.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(startData),
        })
        .then(response => {
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (error) {
                throw new Error();
            }
        })
        .then(data => {
            const moneyValueSpan = document.querySelector('#money_value');
            if (startData.ad === false) {
                moneyValueSpan.textContent = Math.round((moneyValueSpan.textContent - data.result.case_value * document.getElementById("case_count_input").value) * 100) / 100;
                if (!moneyValueSpan.textContent.includes('.')) {
                    moneyValueSpan.textContent = moneyValueSpan.textContent + '.00';
                } else if (moneyValueSpan.textContent.split('.')[1].length == 1) {
                    moneyValueSpan.textContent = moneyValueSpan.textContent + '0';
                }
            }
            const rows = Array.from(document.querySelectorAll('.case_row'));
            const caseContainerWidth = document.querySelector('#case_contents').offsetWidth;
            const itemsPerView = Math.floor(caseContainerWidth / 110);
            const centerOffset = Math.floor(itemsPerView / 2);
            let row_idx = 0;
            rows.forEach(row => {
                const item = Array.from(row.querySelectorAll('.case_item'))[data.result.index + centerOffset];
                item.querySelector('.case_item_image').setAttribute('src', data.result.items[row_idx].image);
                item.querySelector('.rarity_line').setAttribute('style', `color: ${data.result.items[row_idx].rarity_color}; box-shadow: 0px 0px 10px 5px ${data.result.items[row_idx].rarity_color};`);
                item.querySelector('.case_item_name').textContent = data.result.items[row_idx].item_name.split(" | ")[0];
                item.querySelector('.case_item_skin').textContent = data.result.items[row_idx].item_name.split(" | ")[1];
                row_idx++;
            });
            let force_left = data.result.force;
            rows.forEach(row => {
                const item = row.querySelector('.case_item');
                item.animate(
                    [
                        {marginLeft: `0px`},
                        {marginLeft: `-${force_left}px`},
                    ],
                    {
                        duration: 7000,
                        easing: "cubic-bezier(0.3, 0.85, 0.55, 1)",
                        fill: "forwards"
                    }
                ).onfinish = () => {
                    force_left = 0;
                    if (open_case_button && parseInt(moneyValueSpan.textContent) >= data.result.case_value) { open_case_button.removeAttribute("disabled") };
                    if (open_case_ad_button) { open_case_ad_button.removeAttribute("disabled") };
                };
            });
        })
        .catch(() => {
            location.reload();
        });
    }
}

function toggleItemsPanel() {
    const checkboxes = document.querySelectorAll('.item_checkbox');
    const itemsPanel = document.querySelector('#items_panel');
    
    const anySelected = Array.from(checkboxes).some(checkbox => checkbox.checked);
    
    itemsPanel.style.display = anySelected ? 'flex' : 'none';
}

if (document.querySelector('#select_all')) {
    document.querySelector('#select_all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.item_checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        toggleItemsPanel();
    });

    document.querySelector('#unselect_all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.item_checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        toggleItemsPanel();
    });

    document.querySelector('#sell_all').addEventListener('click', function() {
        const selectedItems = [];
        const checkboxes = document.querySelectorAll('.item_checkbox:checked');
        
        checkboxes.forEach(checkbox => {
            selectedItems.push(checkbox.getAttribute('data-id'));
        });
        
        if (selectedItems.length > 0 && confirm(`Are you sure you want to sell all selected ${checkboxes.length} items?\n\nTHIS ACTION IS IRREVERSABLE!`)) {
            document.querySelector('#id_item_input').value = selectedItems.join(',');
            document.querySelector('#id_item_form').submit();
        }
    });

    document.querySelectorAll('.item_checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', toggleItemsPanel);
    });
}

document.querySelectorAll('.profile_mode_button ').forEach(mode_button => {
    mode_button.addEventListener('click', function() {
        const mode = this.dataset.mode;
        const mode_form = document.querySelector("#mode_form");
        const mode_input = document.querySelector("#mode_input");
        
        mode_input.value = mode;
        mode_form.submit();
    });
});

function redirectPost(url, data) {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = url;

    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

const upgradeButton = document.querySelector('#upgrader_button');
if (upgradeButton) {
    let upgradeItem = document.querySelector('#item_to_upgrade').value;
    let upgradeId = null;
    const pointer = document.querySelector('#upgrader_win_pointer');
    upgradeButton.addEventListener('click', function() {
        upgradeButton.setAttribute('disabled', '');
        Array.from(document.querySelectorAll('.upgrader_item_block')).forEach(item => {
            item.removeAttribute('onclick');
        });
        fetch("https://localhost/keydrop/upgrade.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id_item_old: upgradeItem,
                id_item_new: upgradeId,
            }),
        })
        .then(response => {
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (error) {
                throw new Error();
            }
        })
        .then(data => {
            let finish = data.result.finish;
            let success = data.result.success;
            setTimeout(() => {
                pointer.animate(
                    [
                        {transform: `rotate(0deg)`},
                        {transform: `rotate(${finish + 360 * (Math.floor(Math.random() * 2) + 4)}deg)`},
                    ],
                    {
                        duration: 5000,
                        easing: "cubic-bezier(0.3, 0.95, 0.65, 1)",
                        fill: "forwards"
                    }
                ).onfinish = () => {
                    if (success) {
                        document.querySelector('#upgrader_circle_chance').innerHTML = `
                            <strong><div style="color: green;" id="upgrader_win_chance">WIN!</div></strong><br>
                        `;
                        setTimeout(() => {
                            redirectPost("https://localhost/keydrop/upgrader",{
                                "id_item": document.querySelector("#item_to_upgrade").value
                            });
                        }, 1000);
                    } else {
                        document.querySelector('#upgrader_circle_chance').innerHTML = `
                            <strong><div style="color: red;" id="upgrader_win_chance">LOSE!</div></strong><br>
                        `;
                        setTimeout(() => {
                            window.location.href = 'https://localhost/keydrop/inventory';
                        }, 1000);
                    }
                }
            }, 500);
        })
        .catch(() => {
            window.location.href = 'https://localhost/keydrop/inventory';
        });
    });

    function setWinArea(percentage) {
        document.querySelector('#upgrader_win_circle').style.background = `conic-gradient(
            rgba(126, 82, 6, 0.3) 0deg ${percentage * 360}deg,
            transparent ${percentage * 360}deg 360deg
        )`;
    }

    function chooseItem(itemID) {        
        const itemToCopy = document.querySelector(`#chooseUpgrade${itemID}`);
        document.querySelector('#upgrader_new_item').innerHTML = `
            <div class="case_block" style="border-radius: 10px; padding: 10px; ${itemToCopy.style.cssText}">
                ${itemToCopy.innerHTML}
            </div>
        `;
        upgradeId = itemID;
        const itemCost = itemToCopy.querySelector('.item_value').textContent.split(' ', 1)[0];
        const percentage = document.querySelector('#item_to_upgrade_value').value / itemCost;
        setWinArea(percentage);
        document.querySelector('#upgrader_win_chance').textContent = `${Math.round(percentage * 10000) / 100}%`;
        upgradeButton.removeAttribute('disabled');
        window.location.href = "#";
    }
}