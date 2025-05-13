/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

document.getElementById('bt_getCredentialsPlugin').addEventListener('click', function() {
    var idInput = document.querySelector('.configKey[data-l1key="id"]');
    var passwordInput = document.querySelector('.configKey[data-l1key="password"]');

    if (idInput.value === '' || passwordInput.value === '') {
        jeedomUtils.showAlert({
            message: '{{Veuillez entrer un identifiant et un mot de passe de connexion.}}',
            level: 'danger'
        });
        return;
    }

    domUtils.ajax({
        type: "POST",
        url: "plugins/lgthinq2/core/ajax/lgthinq2.ajax.php",
        data: {
            action: "getCredentials"
        },
        dataType: 'json',
        async: true,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                });
                return;
            }
            jeedomUtils.showAlert({
                message: '{{Connexion réalisée avec succès.}}',
                level: 'success'
            });
            document.querySelector('.bt_refreshPluginInfo').click();
        }
    });
});

document.getElementById('bt_validTermsPlugin').addEventListener('click', function() {
    domUtils.ajax({
        type: "POST",
        url: "plugins/lgthinq2/core/ajax/lgthinq2.ajax.php",
        data: {
            action: "validTerms"
        },
        dataType: 'json',
        async: true,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                });
                return;
            }
            jeedomUtils.showAlert({
                message: '{{Conditions validées avec succès.}}',
                level: 'success'
            });
        }
    });
});
var divPluginConfiguration = document.getElementById('div_plugin_configuration');
divPluginConfiguration?.addEventListener('change', function () {
    var expiresInput = document.querySelector('.configKey[data-l1key="expires_in"]');
    var currentTime = Math.floor(Date.now() / 1000);

    if (expiresInput && expiresInput.value != '') {
        var expiresValue = parseInt(expiresInput.value);

        if (expiresValue < currentTime) {
            expiresInput.value = '{{Expiré}}';
            expiresInput.classList.add('dangerBgColor');
        } else {
            expiresInput.classList.remove('dangerBgColor');
            expiresInput.value = expiresValue - currentTime;
        }

        expiresInput.classList.remove('configKey');
        expiresInput.classList.add('configKeyUnsaved');
    }
});

function printPluginConfiguration() {
    var divPluginLGConfiguration = document.getElementById('configuration_plugin_lgthinq2');
    var btnSavePluginConfig = document.getElementById('bt_savePluginConfig');
    var configInputs = divPluginLGConfiguration?.querySelectorAll('.configKey');
    var modificationCount = 0;
    var initialValues = new Map();
    var modificationMessage = document.createElement('i');
    modificationMessage.classList.add('modificationWithoutSave', 'label', 'label-warning', 'pull-right');
    modificationMessage.innerHTML = '{{Modification en cours...}}';
    modificationMessage.unseen();
    btnSavePluginConfig.parentNode.insertBefore(modificationMessage, btnSavePluginConfig.nextSibling);

    function resetStyle(element) {
        element.style.setProperty('background-color', '', 'important');
        element.style.setProperty('color', '', 'important');
    }

    function setModifiedStyle(element) {
        element.style.setProperty('background-color', 'var(--al-warning-color)', 'important');
        element.style.setProperty('color', 'var(--sc-lightTxt-color)', 'important');
    }

    function updateModificationStatus() {
        if (modificationCount > 0) {
            modificationMessage.seen();
        } else {
            modificationMessage.unseen();
        }
    }

    configInputs?.forEach(function (input) {
        resetStyle(input); // Reset du style au démarrage
        if (input.type === 'checkbox') {
            initialValues.set(input, input.checked);
        } else {
            initialValues.set(input, input.value);
        }
    });

    configInputs?.forEach(function (input) {
        if (input.type === 'checkbox') {
            input.addEventListener('change', function() {
                const initialValue = initialValues.get(this);
                const isModified = this.checked !== initialValue;

                if (isModified && !this.hasAttribute('data-modified')) {
                    setModifiedStyle(this);
                    this.setAttribute('data-modified', '');
                    modificationCount++;
                } else if (!isModified && this.hasAttribute('data-modified')) {
                    resetStyle(this);
                    this.removeAttribute('data-modified');
                    modificationCount--;
                }
                updateModificationStatus();
            });
        } else {
            const eventType = input.nodeName === 'SELECT' ? 'change' : 'input';
            input.addEventListener(eventType, function() {
                const initialValue = initialValues.get(this);
                const isModified = this.value !== initialValue;

                if (isModified && !this.hasAttribute('data-modified')) {
                    setModifiedStyle(this);
                    this.setAttribute('data-modified', '');
                    modificationCount++;
                } else if (!isModified && this.hasAttribute('data-modified')) {
                    resetStyle(this);
                    this.removeAttribute('data-modified');
                    modificationCount--;
                }
                updateModificationStatus();
            });
        }
    });

    btnSavePluginConfig.addEventListener('click', function() {
        configInputs.forEach(input => {
            if (input.type === 'checkbox') {
                initialValues.set(input, input.checked);
            } else {
                initialValues.set(input, input.value);
            }
            resetStyle(input);
            input.removeAttribute('data-modified');
        });
        modificationCount = 0;
        modificationMessage.unseen();
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", printPluginConfiguration);
} else {
    setTimeout(function() {
        printPluginConfiguration();
    }, 100)
}
