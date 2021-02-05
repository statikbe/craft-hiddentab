const hideFields = function () {
    fieldsToHide.forEach(element => {
            field = document.querySelectorAll("div[id*=" + element + "]"),
                field.forEach(element => {
                    element.style.display = 'none';
                });
        }
    );
}


const onDynamicContent = function (
    parent,
    selector,
    callback
) {
    const mutationObserver = new MutationObserver(
        (mutationsList) => {
            for (let mutation of mutationsList) {
                if (mutation.type === "childList") {
                    Array.from(mutation.addedNodes).forEach((node) => {
                        if (node.nodeType == 1) {
                            const results = node.querySelectorAll(selector);
                            if (results.length > 0) {
                                callback(results);
                            } else {
                                if (node.matches(selector)) {
                                    callback([node]);
                                }
                            }
                        }
                    });
                }
            }
        }
    );
    mutationObserver.observe(parent, {
        attributes: false,
        childList: true,
        subtree: true,
    });
}

onDynamicContent(document.documentElement, '.hud', hideFields);