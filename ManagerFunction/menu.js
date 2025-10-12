export function createThreeDotMenu(actions = []) {
    // three-dot button
    const menuContainer = document.createElement("div");
    menuContainer.className = "three-dot-menu-container";
    menuContainer.style.position = "relative";

    const button = document.createElement("button");
    button.className = "three-dot-btn";
    button.innerHTML = "&#8942;"; // vertical ellipsis
    button.style.fontSize = "25px";
    button.style.background = "none";
    button.style.border = "none";
    button.style.borderRadius = "50%";
    button.style.width = "30px";
    button.style.height = "30px";
    button.style.cursor = "pointer";
    button.style.padding = "0";
    button.style.margin = "-20px 0px 10px 0px";
    // hover effect
    button.onmouseover = () => {
        button.style.backgroundColor = "lightgray";
    };
    button.onmouseout = () => {
        button.style.backgroundColor = "transparent";
    };

    // dropdown menu
    const menu = document.createElement("div");
    menu.className = "three-dot-dropdown";
    menu.style.display = "none";
    // aaa
    // menu.style.position = "absolute";
    menu.style.position = "fixed";
    menu.style.right = "0";
    menu.style.background = "#fff";
    menu.style.border = "1px solid #ccc";
    menu.style.zIndex = "9999";
    menu.style.boxShadow = "0 2px 8px rgba(0, 0, 0, 0.15)";
    menu.style.borderRadius = "10px";
    menu.style.maxWidth = "130px";

    actions.forEach((action, index) => {
        const item = document.createElement("div");
        const hr = document.createElement("hr");
        item.className = "three-dot-item";
        item.textContent = action.label;
        item.style.padding = "8px 16px";
        item.style.cursor = "pointer";
        if(action.label.toLowerCase().includes("delete") || action.label.toLowerCase().includes("kick")){
            item.style.color = "red";
        }
        // hover effect
        item.onmouseover = () => {
            item.style.backgroundColor = "lightgray";
            item.style.borderRadius = "10px";
        };
        item.onmouseout = () => {
            item.style.backgroundColor = "transparent";
        };
        item.addEventListener("click", (e) => {
            e.stopPropagation();
            menu.style.display = "none";
            action.onClick();
        });
        menu.appendChild(item);

        if(!index === actions.length - 1){
            hr.style.margin = "0";
            hr.style.border = "1px solid #eee";
            menu.appendChild(hr);
        }
    });

    function positionDropdown() {
        const rect = button.getBoundingClientRect();
        menu.style.top = `${rect.bottom + 5}px`; // 5px below button
        menu.style.left = `${rect.left - 110}px`;
    }
    button.addEventListener("click", (e) => {
        e.stopPropagation();
        // const rect = button.getBoundingClientRect();
        // menu.style.top = `${rect.bottom + 5}px`;
        // menu.style.left = `${rect.left - 110}px`;
        positionDropdown();
        menu.style.display = menu.style.display === "none" ? "block" : "none";
    });

    // Hide menu when clicking outside or scrolling
    document.addEventListener("click", () => {
        menu.style.display = "none";
    });
    window.addEventListener("scroll", () => {
        if (menu.style.display === "block") {
            menu.style.display = "none";
        }
    }, true);
    menuContainer.appendChild(button);
    menuContainer.appendChild(menu);

    return menuContainer;
}

