/**
 * Magento Variables plugin for Redactor
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
define(["jquery", "mage/translate", "bluefoot/config"], function(jQuery, $t, Config) {

    /**
     * Override the default function to accept a bluefoot update callback
     * @param value
     */
    window.MagentovariablePlugin.insertVariableSuper = window.MagentovariablePlugin.insertVariable;
    window.MagentovariablePlugin.insertVariable = function (value) {
        if (typeof this.bluefootUpdate === 'function') {
            this.bluefootUpdate(value);
            Variables.closeDialogWindow();
            this.bluefootUpdate = null;
            return;
        }

        this.insertVariableSuper(value);
    };

    /**
     * Redactor plugin
     */
    return function() {
        jQuery.Redactor.prototype.magentoVars = function () {
            return {
                init: function () {
                    var button = this.button.add('magentoVars', '<i class="fa fa-code" alt="' + $t('Variables') + '"></i>');
                    this.button.addCallback(button, this.magentoVars.show);
                },
                show: function () {
                    MagentovariablePlugin.loadChooser(Config.getPluginConfig("gene_redactor", "magentovar_url"), null);
                    MagentovariablePlugin.bluefootUpdate = function (value) {
                        this.magentoVars.insert(value);
                    }.bind(this);
                },
                insert: function (html) {
                    this.buffer.set();
                    this.air.collapsedEnd();
                    this.insert.html(html);
                }
            };
        };
    };
});