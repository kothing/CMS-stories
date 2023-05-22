class PluginManagement {
    init() {
        $('#plugin-list').on('click', '.btn-trigger-change-status', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass('button-loading');

            $.ajax({
                url: route('plugins.change.status', {name: _self.data('plugin')}),
                type: 'POST',
                data: {'_method': 'PUT'},
                success: data => {
                    if (data.error) {
                        Botble.showError(data.message);
                    } else {
                        Botble.showSuccess(data.message);
                        $('#plugin-list #app-' + _self.data('plugin')).load(window.location.href + ' #plugin-list #app-' + _self.data('plugin') + ' > *');
                        window.location.reload();
                    }
                    _self.removeClass('button-loading');
                },
                error: data => {
                    Botble.handleError(data);
                    _self.removeClass('button-loading');
                }
            });
        });

        $(document).on('click', '.btn-trigger-remove-plugin', event => {
            event.preventDefault();
            $('#confirm-remove-plugin-button').data('plugin', $(event.currentTarget).data('plugin'));
            $('#remove-plugin-modal').modal('show');
        });

        $(document).on('click', '#confirm-remove-plugin-button', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass('button-loading');

            $.ajax({
                url: route('plugins.remove', {plugin: _self.data('plugin')}),
                type: 'POST',
                data: {'_method': 'DELETE'},
                success: data => {
                    if (data.error) {
                        Botble.showError(data.message);
                    } else {
                        Botble.showSuccess(data.message);
                        window.location.reload();
                    }
                    _self.removeClass('button-loading');
                    $('#remove-plugin-modal').modal('hide');
                },
                error: data => {
                    Botble.handleError(data);
                    _self.removeClass('button-loading');
                    $('#remove-plugin-modal').modal('hide');
                }
            });
        });

        $(document).on('click', '.btn-trigger-update-plugin', event => {
            event.preventDefault();

            let _self = $(event.currentTarget);
            let uuid = _self.data('uuid');

            _self.addClass('button-loading');
            _self.attr('disabled', true);

            $.ajax({
                url: route('plugins.marketplace.ajax.update', {id: uuid}),
                type: 'POST',
                success: data => {
                    if (data.error) {
                        Botble.showError(data.message);

                        _self.removeClass('button-loading');
                        _self.removeAttr('disabled', true);

                        if (data.data && data.data.redirect) {
                            window.location.href
                        }
                    } else {
                        Botble.showSuccess(data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                },
                error: data => {
                    Botble.handleError(data);
                    _self.removeClass('button-loading');
                    _self.removeAttr('disabled', true);
                }
            });
        });

        this.checkUpdate();
    }

    checkUpdate() {
        $.ajax({
            url: route('plugins.marketplace.ajax.check-update'),
            type: 'POST',
            success: data => {
                if (data.data) {
                    Object.keys(data.data).forEach((key) => {

                        const plugin = data.data[key];

                        const element = $('[data-check-update="' + plugin.name + '"]');

                        $checkVersion = this.checkVersion(element.data('version'), plugin.version);

                        if ($checkVersion) {
                            element.attr('style', 'display: show;');
                            element.attr('data-uuid', plugin.id);
                        }

                    });
                }
            }
        });
    }

    checkVersion(currentVersion, latestVersion) {
        const current = currentVersion.toString().split('.');
        const latest = latestVersion.toString().split('.');

        const length = Math.max(current.length, latest.length);

        for (let i = 0; i < length; i++) {
            const oldVer = ~~current[i];
            const newVer = ~~latest[i];

            if (newVer > oldVer) {
                return true;
            }
        }
        return false;
    }
}

$(document).ready(() => {
    new PluginManagement().init();
});
