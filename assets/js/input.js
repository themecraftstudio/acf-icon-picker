(function($){
    let settings; // unique to each field type (not instance!). See icon-picker-field-v5.php:__construct()
    let dialog;

    acf.addAction('ready_field/type=icon-picker', function (field) {
        settings = acf.get('icon_picker');

        setUpDialog(settings);
        
        let fieldContainer = field.$el[0];
        fieldContainer.querySelector('div.icon-preview > a')
            .addEventListener('click', function () {
                let _listener = function (e) {
                    if (e.type === 'icon-picked')
                        field.$input()[0].value = e.detail;
                }

                dialog.dialog.addEventListener('icon-picked', _listener);
                dialog.show();
                dialog.on('hide', function () {
                    dialog.dialog.removeEventListener('icon-picked', _listener);
                });
            });
    });

    function setUpDialog(field) {
        if (dialog)
            return;

        let lis = '';
        for (const icon of settings['icons']) {
            lis += `<li data-id="${icon['id']}"><a><img src="${icon['url']}"></a></li>`
        }

        let t = document.createElement('template');
        t.innerHTML =
        `<div id="dialog-container">
            <div tabindex="-1" data-a11y-dialog-hide></div>
            <dialog aria-labelledby="acf-icon-picker-title">
                <button type="button" data-a11y-dialog-hide aria-label="Close this dialog window">&times;</button>

                <h1 id="dialog-title">Pick the coolest icon</h1>

                <form role="search">
                    <input type="search" placeholder="Filter icons" aria-label="Search icons">
                </form>

                <p>Pick the coolest icon</p>

                <ul class="icons">${lis}</ul>
            </dialog>
        </div>`;

        let node = document.importNode(t.content, true); // returns DocumentFragment
        document.body.appendChild(node); // *moves* children from DocumentFrament to document.body
        
        dialog = new A11yDialog(document.body.lastElementChild);

        for (const iconListItem of dialog.dialog.querySelectorAll('ul.icons > li')) {
            iconListItem.addEventListener('click', function (e) {
                let imageSelectedEvent = new CustomEvent('icon-picked', {
                    'bubbles': true,
                    'detail': e.currentTarget.dataset.id
                });
                this.dispatchEvent(imageSelectedEvent);
                
                dialog.hide();
                
                e.stopPropagation();
            });
        }
    };

})(jQuery);