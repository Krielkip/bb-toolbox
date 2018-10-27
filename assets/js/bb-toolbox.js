(function ($) {

    BBToolbox = {

        _cbVal: [],
        _preview: [],

        /**
         * Initializes the events.
         *
         * @since 1.0
         * @access private
         * @method _init
         */
        _init: function () {
            BBToolbox._bindEvents();
        },

        /**
         * Binds the events to fields.
         *
         * @since 1.0
         * @access private
         * @method _bindEvents
         */
        _bindEvents: function () {
            $('body').delegate('.fl-builder-bar-actions .fl-builder-toolbox-button', 'click', BBToolbox._openToolboxPanelClicked);
            $('body').delegate('.fl-builder-toolbox-settings .fl-builder-settings-save', 'click', BBToolbox._saveToolboxPanelClicked);
        },

        _openToolboxPanelClicked: function () {
            FLBuilderSettingsForms.render({
                id: 'bb_toolbox_form',
                className: 'fl-builder-toolbox-settings',
                settings: FLBuilderConfig.toolboxSettings,
                tabs: FLBuilderConfig.toolboxTabs
            });
        },

        _saveToolboxPanelClicked: function () {
            var form = $(this).closest('.fl-builder-settings'),
                valid = form.validate().form(),
                settings = FLBuilder._getSettings(form);

            if (valid) {
                FLBuilder.showAjaxLoader();

                FLBuilder.ajax({
                    action: 'save_toolbox_settings',
                    settings: settings
                }, BBToolbox._saveToolboxPanelComplete);
                FLBuilder._lightbox.close();
            }
        },

        _saveToolboxPanelComplete: function (response) {
            FLBuilderConfig.toolboxSettings = JSON.parse(response);

            if (FLBuilderConfig.toolboxSettings.newlink) {
                var url     = FLBuilderConfig.homeUrl + "?p=" + FLBuilderConfig.postId + "&fl_builder";
                setTimeout(function () {
                    window.location.replace(url)
                }, 1000);
            }
            else {
                FLBuilder._updateLayout();
            }
        }
    };

    BBToolbox._init();

})(jQuery);
