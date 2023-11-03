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
        $.fn.showAlert({
            message: '{{Veuillez entrer un identifiant et un mot de passe de connexion.}}',
            level: 'danger'
        });
        return;
    }
});

document.getElementById('div_plugin_configuration').addEventListener('change', function () {
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
