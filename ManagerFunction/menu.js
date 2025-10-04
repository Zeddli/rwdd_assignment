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
    menu.style.position = "absolute";
    menu.style.right = "0";
    menu.style.background = "#fff";
    menu.style.border = "1px solid #ccc";
    menu.style.zIndex = "1000";
    menu.style.boxShadow = "0 2px 8px rgba(0, 0, 0, 0.15)";

    actions.forEach(action => {
        const item = document.createElement("div");
        const hr = document.createElement("hr");
        item.className = "three-dot-item";
        item.textContent = action.label;
        item.style.padding = "8px 16px";
        item.style.cursor = "pointer";
        // hover effect
        item.onmouseover = () => {
            item.style.backgroundColor = "lightgray";
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

        hr.style.margin = "0";
        hr.style.border = "1px solid #eee";
        menu.appendChild(hr);
    });

    button.addEventListener("click", (e) => {
        e.stopPropagation();
        menu.style.display = menu.style.display === "none" ? "block" : "none";
    });

    // Hide menu when clicking outside
    document.addEventListener("click", () => {
        menu.style.display = "none";
    });

    menuContainer.appendChild(button);
    menuContainer.appendChild(menu);

    return menuContainer;
}

