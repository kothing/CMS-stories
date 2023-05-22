import {RecentItems} from '../Config/MediaConfig';
import {Helpers} from '../Helpers/Helpers';
import {MessageService} from './MessageService';
import Cropper from 'cropperjs';
import scrollspy from "bootstrap/js/src/scrollspy";

export class ActionsService {
    static handleDropdown() {
        let selected = _.size(Helpers.getSelectedItems());

        ActionsService.renderActions();

        if (selected > 0) {
            $('.rv-dropdown-actions').removeClass('disabled');
        } else {
            $('.rv-dropdown-actions').addClass('disabled');
        }
    }

    static handlePreview() {
        let selected = [];

        _.each(Helpers.getSelectedFiles(), value => {
            if (value.preview_url) {
                selected.push({
                    src: value.preview_url,
                    type: value.preview_type,
                });
                RecentItems.push(value.id);
            }
        });

        if (_.size(selected) > 0) {
            $.fancybox.open(selected);
            Helpers.storeRecentItems();
        } else {
            this.handleGlobalAction('download');
        }
    }

    static renderCropImage() {
        const html = $('#rv_media_crop_image').html()
        const modal = $('#modal_crop_image .crop-image').empty()
        const item = Helpers.getSelectedItems()[0]
        const form = $('#modal_crop_image .form-crop')
        let cropData;

        const el = html.replace(/__src__/gi, item.full_url)
        modal.append(el)

        const image = modal.find('img')[0]

        const options = {
            minContainerWidth: 550,
            minContainerHeight: 550,
            dragMode: 'move',
            crop(event) {
                cropData = event.detail
                form.find('input[name="image_id"]').val(item.id)
                form.find('input[name="crop_data"]').val(JSON.stringify(cropData))
                setHeight(cropData.height)
                setWidth(cropData.width)
            }
        }
        let cropper = new Cropper(image, options)

        form.find('#aspectRatio').on('click', function () {
            cropper.destroy()
            if ($(this).is(':checked')) {
                options.aspectRatio = cropData.width/cropData.height
            } else {
                options.aspectRatio = null
            }
            cropper = new Cropper(image, options)
        })

        form.find('#dataHeight').on('change', function () {
            cropData.height = parseFloat($(this).val())
            cropper.setData(cropData)
            setHeight(cropData.height)
        })

        form.find('#dataWidth').on('change', function () {
            cropData.width = parseFloat($(this).val())
            cropper.setData(cropData)
            setWidth(cropData.width)
        })

        const setHeight = (height) => {
            form.find('#dataHeight').val(parseInt(height))
        }

        const setWidth = (width) => {
            form.find('#dataWidth').val(parseInt(width))
        }
    }

    static handleCopyLink() {
        let links = '';
        _.each(Helpers.getSelectedFiles(), value => {
            if (!_.isEmpty(links)) {
                links += '\n';
            }
            links += value.full_url;
        });
        let $clipboardTemp = $('.js-rv-clipboard-temp');
        $clipboardTemp.data('clipboard-text', links);
        new Clipboard('.js-rv-clipboard-temp', {
            text: () => {
                return links;
            }
        });
        MessageService.showMessage('success', RV_MEDIA_CONFIG.translations.clipboard.success, RV_MEDIA_CONFIG.translations.message.success_header);
        $clipboardTemp.trigger('click');
    }

    static handleGlobalAction(type, callback) {
        let selected = [];
        _.each(Helpers.getSelectedItems(), value => {
            selected.push({
                is_folder: value.is_folder,
                id: value.id,
                full_url: value.full_url
            });
        });

        switch (type) {
            case 'rename':
                $('#modal_rename_items').modal('show').find('form.rv-form').data('action', type);
                break;
            case 'copy_link':
                ActionsService.handleCopyLink();
                break;
            case 'preview':
                ActionsService.handlePreview();
                break;
            case 'alt_text':
                $('#modal_alt_text_items').modal('show').find('form.rv-form').data('action', type);
                break;
            case 'crop':
                $('#modal_crop_image').modal('show').find('form.rv-form').data('action', type);
                break;
            case 'trash':
                $('#modal_trash_items').modal('show').find('form.rv-form').data('action', type);
                break;
            case 'delete':
                $('#modal_delete_items').modal('show').find('form.rv-form').data('action', type);
                break;
            case 'empty_trash':
                $('#modal_empty_trash').modal('show').find('form.rv-form').data('action', type);
                break;
            case 'download':
                let files = []
                _.each(Helpers.getSelectedItems(), value => {
                    if (!_.includes(Helpers.getConfigs().denied_download, value.mime_type)) {
                        files.push({
                            id: value.id,
                            is_folder: value.is_folder,
                        })
                    }
                });

                if (files.length) {
                    ActionsService.handleDownload(files)
                } else {
                    MessageService.showMessage('error', RV_MEDIA_CONFIG.translations.download.error, RV_MEDIA_CONFIG.translations.message.error_header);
                }
                break;
            default:
                ActionsService.processAction({
                    selected: selected,
                    action: type
                }, callback);
                break;
        }
    }

    static processAction(data, callback = null) {
        $.ajax({
            url: RV_MEDIA_URL.global_actions,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: () => {
                Helpers.showAjaxLoading();
            },
            success: res => {
                Helpers.resetPagination();
                if (!res.error) {
                    MessageService.showMessage('success', res.message, RV_MEDIA_CONFIG.translations.message.success_header);
                } else {
                    MessageService.showMessage('error', res.message, RV_MEDIA_CONFIG.translations.message.error_header);
                }
                if (callback) {
                    callback(res);
                }
            },
            complete: () => {
                Helpers.hideAjaxLoading();
            },
            error: data => {
                MessageService.handleError(data);
            }
        });
    }

    static renderRenameItems() {
        let VIEW = $('#rv_media_rename_item').html();
        let $itemsWrapper = $('#modal_rename_items .rename-items').empty();

        _.each(Helpers.getSelectedItems(), (value) => {
            let item = VIEW
                .replace(/__icon__/gi, value.icon || 'fa fa-file')
                .replace(/__placeholder__/gi, 'Input file name')
                .replace(/__value__/gi, value.name)
            ;
            let $item = $(item);
            $item.data('id', value.id);
            $item.data('is_folder', value.is_folder);
            $item.data('name', value.name);
            $itemsWrapper.append($item);
        });
    }

    static renderAltTextItems() {
        let VIEW = $('#rv_media_alt_text_item').html();
        let $itemsWrapper = $('#modal_alt_text_items .alt-text-items').empty();

        _.each(Helpers.getSelectedItems(), (value) => {
            let item = VIEW
                .replace(/__icon__/gi, value.icon || 'fa fa-file')
                .replace(/__placeholder__/gi, 'Input file alt')
                .replace(/__value__/gi, value.alt === null ? '' : value.alt)
            ;
            let $item = $(item);
            $item.data('id', value.id);
            $item.data('alt', value.alt);
            $itemsWrapper.append($item);
        });
    }

    static renderActions() {
        let hasFolderSelected = Helpers.getSelectedFolder().length > 0;

        let ACTION_TEMPLATE = $('#rv_action_item').html();
        let initializedItem = 0;
        let $dropdownActions = $('.rv-dropdown-actions .dropdown-menu');
        $dropdownActions.empty();

        let actionsList = $.extend({}, true, Helpers.getConfigs().actions_list);

        if (hasFolderSelected) {
            actionsList.basic = _.reject(actionsList.basic, item => {
                return item.action === 'preview';
            });
            actionsList.basic = _.reject(actionsList.basic, item => {
                return item.action === 'crop';
            });
            actionsList.file = _.reject(actionsList.file, item => {
                return item.action === 'alt_text';
            });
            actionsList.file = _.reject(actionsList.file, item => {
                return item.action === 'copy_link';
            });

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.create')) {
                actionsList.file = _.reject(actionsList.file, item => {
                    return item.action === 'make_copy';
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.edit')) {
                actionsList.file = _.reject(actionsList.file, item => {
                    return _.includes(['rename'], item.action);
                });

                actionsList.user = _.reject(actionsList.user, item => {
                    return _.includes(['rename'], item.action);
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.trash')) {
                actionsList.other = _.reject(actionsList.other, item => {
                    return _.includes(['trash', 'restore'], item.action);
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.destroy')) {
                actionsList.other = _.reject(actionsList.other, item => {
                    return _.includes(['delete'], item.action);
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.favorite')) {
                actionsList.other = _.reject(actionsList.other, item => {
                    return _.includes(['favorite', 'remove_favorite'], item.action);
                });
            }
        }

        let selectedFiles = Helpers.getSelectedFiles();

        let canPreview = _.filter(selectedFiles, function (value) {
            return value.preview_url;
        }).length;

        if (!canPreview) {
            actionsList.basic = _.reject(actionsList.basic, item => {
                return item.action === 'preview';
            });
        }

        let fileIsImage = _.filter(selectedFiles, function (value) {
            return value.type === 'image';
        }).length;

        if (! fileIsImage) {
            actionsList.basic = _.reject(actionsList.basic, item => {
                return item.action === 'crop';
            });

            actionsList.file = _.reject(actionsList.file, item => {
                return item.action === 'alt_text';
            });
        }

        if (selectedFiles.length > 0) {
            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.create')) {
                actionsList.file = _.reject(actionsList.file, item => {
                    return item.action === 'make_copy';
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.edit')) {
                actionsList.file = _.reject(actionsList.file, item => {
                    return _.includes(['rename'], item.action);
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.trash')) {
                actionsList.other = _.reject(actionsList.other, item => {
                    return _.includes(['trash', 'restore'], item.action);
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.destroy')) {
                actionsList.other = _.reject(actionsList.other, item => {
                    return _.includes(['delete'], item.action);
                });
            }

            if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.favorite')) {
                actionsList.other = _.reject(actionsList.other, item => {
                    return _.includes(['favorite', 'remove_favorite'], item.action);
                });
            }

            if (selectedFiles.length > 1) {
                actionsList.basic = _.reject(actionsList.basic, item => {
                    return item.action === 'crop';
                });
            }
        }

        _.each(actionsList, (action, key) => {
            _.each(action, (item, index) => {
                let is_break = false;
                switch (Helpers.getRequestParams().view_in) {
                    case 'all_media':
                        if (_.includes(['remove_favorite', 'delete', 'restore'], item.action)) {
                            is_break = true;
                        }
                        break;
                    case 'recent':
                        if (_.includes(['remove_favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                            is_break = true;
                        }
                        break;
                    case 'favorites':
                        if (_.includes(['favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                            is_break = true;
                        }
                        break;
                    case 'trash':
                        if (!_.includes(['preview', 'delete', 'restore', 'rename', 'download'], item.action)) {
                            is_break = true;
                        }
                        break;
                }
                if (!is_break) {
                    let template = ACTION_TEMPLATE
                        .replace(/__action__/gi, item.action || '')
                        .replace(/__icon__/gi, item.icon || '')
                        .replace(/__name__/gi, RV_MEDIA_CONFIG.translations.actions_list[key][item.action] || item.name);
                    if (!index && initializedItem) {
                        template = '<li role="separator" class="divider"></li>' + template;
                    }
                    $dropdownActions.append(template);
                }
            });

            if (action.length > 0) {
                initializedItem++;
            }
        });
    }

    static handleDownload(files) {
        const html = $('.media-download-popup')
        let downloadTimeout = null
        $.ajax({
            url: RV_MEDIA_URL.download,
            method: 'POST',
            data: {selected: files},
            xhrFields: {
                responseType: 'blob'
            },
            beforeSend: () => {
                downloadTimeout = setTimeout(() => {
                    html.show()
                }, 1000)
            },
            success: (response, status, xhr) => {
                const downloadUrl = URL.createObjectURL(response);
                const a = document.createElement('a');
                const fileName = xhr.getResponseHeader('Content-Disposition').split('filename=')[1].split(';')[0];
                a.href = downloadUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click()
                a.remove()
                window.URL.revokeObjectURL(downloadUrl);
            },
            complete: () => {
                html.hide()
                clearTimeout(downloadTimeout)
            },
            error: (data) => {
                MessageService.handleError(data)
            }
        })
    }
}
