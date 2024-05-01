<?php

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

try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect('admin')) {
		throw new Exception('401 Unauthorized');
	}

	ajax::init();

    if (init('action') == 'getCredentials') {
        $result = lgthinq2::login();
        ajax::success($result);
    }

    if (init('action') == 'synchronize') {
        if (config::byKey('id', 'lgthinq2', '') == '' || config::byKey('password', 'lgthinq2', '') == '') {
            $result = false;
        } else {
            if (init('deleteCmds') === true) {
                $eqLogic = lgthinq2::byId(init('id'));
                if (!is_object($eqLogic)) {
                    throw new Exception(__('LGThinq2 eqLogic non trouvé : ', __FILE__) . init('id'));
                }
                foreach ($eqLogic->getCmd() as $cmd) {
                    if ($cmd->getLogicalId() != 'refresh') {
                        $cmd->remove();
                    }
                }
            }
            $result = lgthinq2::synchronize(init('id'));
        }
        ajax::success($result);
    }

    if (init('action') == 'deleteEquipments') {
       if (init('what') == 'all' || init('what') == 'appareils') {
            $eqLogics = eqLogic::byType('lgthinq2');
            if (init('what') == 'appareils') {

            } elseif (init('what') == 'all') {
                foreach ($eqLogics as $eqLogic) {
                    $eqLogic->remove();
                }
            }
        }
        ajax::success();
    }

    if (init('action') == 'getImage') {
        $eqLogic = lgthinq2::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception(__('lgthinq2 eqLogic non trouvé : ', __FILE__) . init('id'));
        }
        $result = $eqLogic->getImage();
        ajax::success($result);
    }

	throw new Exception('Aucune methode correspondante');
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayException($e), $e->getCode());
}
?>