import iframeResize from 'iframe-resizer/js/iframeResizer';

if (typeof vueApp !== 'undefined') {
    vueApp.booting(vue => {
        vue.directive('resize', {
            bind: function (el, {value = {}}) {
                el.addEventListener('load', () => iframeResize(value, el))
            },
            unbind: function (el) {
                el.iFrameResizer.removeListeners();
            }
        });
    });
}
