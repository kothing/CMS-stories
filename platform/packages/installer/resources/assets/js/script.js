'use strict';

class Installer
{
    init() {
        const x = document.getElementById('error_alert');
        const y = document.getElementById('close_alert');
        if (y && x) {
            y.onclick = function () {
                x.style.display = 'none';
            };
        }
    }
}

(new Installer()).init();
