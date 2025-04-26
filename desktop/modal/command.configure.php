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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$cmd = cmd::byId(init('id'));
if (!is_object($cmd)) {
    throw new Exception('{{Commande non trouvée}}'.' : ' . init('id'));
}
global $JEEDOM_INTERNAL_CONFIG;

$cmdInfo = jeedom::toHumanReadable(utils::o2a($cmd));
$cmdInfo['eqLogicName'] = $cmd->getEqLogic()->getName();
sendVarToJS('cmdInfo', $cmdInfo);
?>


<div role="tabpanel">
  <div class="tab-content" id="div_displayCmdConfigure" style="overflow-x:hidden">
  <div class="input-group pull-right" style="display:inline-flex">
    <span class="input-group-btn">
      </a><a class="btn btn-success btn-sm roundedLeft roundedRight" id="bt_cmdConfigureSave"><i class="fas fa-save"></i> {{Sauvegarder}}</a>
    </span>
  </div>
    <div role="tabpanel" class="tab-pane active" id="cmd_information">
      <br/>
      <div class="row">
        <div class="col-sm-9" >
          <form class="form-horizontal">
            <fieldset>
              <div class="form-group">
                <label class="col-xs-3 control-label">{{ID logique}}</label>
                <div class="col-xs-9">
                    <input class="cmdAttr form-control input-sm" data-l1key="logicalId" placeholder="{{ID logique}}" title="{{ID logique}}" style="display:inline-block"></input>
                </div>
              </div>
              </br>
              <?php if ($cmd->getType() == 'info') { ?>
                  <legend><i class="icon kiko-information-symbol"></i> {{Commande information}}</legend>
                  <div class="form-group">
                    <label class="col-xs-3 control-label">{{Valeur par défaut}}</label>
                    <div class="col-xs-9">
                        <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="default" placeholder="{{Valeur par défaut}}" title="{{Valeur par défaut}}" style="display:inline-block"></input>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-xs-3 control-label">{{Sous-type originel}}</label>
                    <div class="col-xs-9">
                        <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="originalType" placeholder="{{Sous-type}}" title="{{Sous-type}}" style="display:inline-block"></input>
                    </div>
                  </div>
                    <?php if ($valueMapping = $cmd->getConfiguration('valueMapping', false)) { ?>
                    <div class="form-group">
                        <label class="col-xs-3 control-label">{{Valeurs possibles}}</label>
                        <div class="col-xs-9">
                        <?php
                            foreach ($valueMapping as $keyM => $valueM) {
                                $valueM = is_array($valueM)?json_encode($valueM):$valueM;
                                echo '<span class="label">' . $keyM . '</span> => <span class="label">' . $valueM . '</span></br>';
                            }
                        }
                    ?>
                    </div>
                  </div>
              <?php } else { ?>
                  <legend><i class="icon kiko-wrench"></i> {{Commande action}}</legend>

                  <div class="form-group">
                    <label class="col-xs-3 control-label">{{cmd}}</label>
                    <div class="col-xs-9">
                        <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="cmd" placeholder="{{cmd}}" title="{{cmd}}" style="display:inline-block"></input>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-xs-3 control-label">{{ctrlKey}}</label>
                    <div class="col-xs-9">
                        <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="ctrlKey" placeholder="{{ctrlKey}}" title="{{ctrlKey}}" style="display:inline-block"></input>
                    </div>
                  </div>

                  <?php if ($cmd->getConfiguration('dataSetList', null) !== null) { ?>
                      <div class="form-group">
                        <label class="col-xs-3 control-label">{{dataSetList}}</label>
                        <div class="col-xs-9">
                            <textarea id="prettyDataSetList" rows="10" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="dataSetList" placeholder="{{dataSetList}}" title="{{dataSetList}}" style="display:inline-block;height: auto;"><?php echo json_encode($cmd->getConfiguration('dataSetList'), JSON_PRETTY_PRINT)?></textarea>
                        </div>
                      </div>
                  <?php } else { ?>
                      <div class="form-group">
                        <label class="col-xs-3 control-label">{{dataKey}}</label>
                        <div class="col-xs-9">
                            <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="dataKey" placeholder="{{dataKey}}" title="{{dataKey}}" style="display:inline-block"></input>
                        </div>
                      </div>

                      <?php if ($cmd->getSubType() == 'select') { ?>
                          <div class="form-group">
                            <label class="col-xs-3 control-label">{{Valeurs possibles}}</label>
                            <div class="col-xs-9">
                                      <textarea class="cmdAttr form-control input-sm tooltipstered" data-l1key="configuration" data-l2key="listValue" placeholder="Liste de valeur|texte séparé par ;" id="changeListValue"></textarea>
                              <?php
                                  $elements = explode(';', $cmd->getConfiguration('listValue', ''));
                                  $i = 0;
                                  foreach ($elements as $element) {
                                      $coupleArray = explode('|', $element);
                                      echo '<input class="form-control input-sm coupleArray" style="display:inline-block;width:200px;" value="'.$coupleArray[1].'" id="coupleKey'.$i.'" > => <input class="form-control input-sm coupleArray" style="display:inline-block;width: 200px;" value="'.$coupleArray[0].'" id="coupleArray'.$i.'" ><br/>';
                                      $i++;
                                  }
                                ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-xs-3 control-label">{{Sélection par index de valeur}}</label>
                            <div class="col-xs-9">
                              <input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="listValueSelected" />
                            </div>
                          </div>
                      <?php } else { ?>
                          <div class="form-group">
                            <label class="col-xs-3 control-label">{{dataValue}}</label>
                            <div class="col-xs-9">
                                <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="dataValue" placeholder="{{dataValue}}" title="{{dataValue}}" style="display:inline-block"></input>
                            </div>
                          </div>
                      <?php } ?>
                  <?php } ?>
              <?php } ?>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    console.log(cmdInfo);
    let title = '{{Configuration commande}}';
    title += ' : ' + cmdInfo.eqLogicName;
    title += ' <span class="cmdName">[' + cmdInfo.name + '] <em>(' + cmdInfo.type + ')</em></span>';

    const parentDialog = document.querySelector('#div_displayCmdConfigure').closest('.ui-dialog');
    if (parentDialog) {
        parentDialog.querySelector('.ui-dialog-title').innerHTML = title;
    }

    if (document.getElementById('eqLogicConfigureTab')) {
        document.querySelector('#cmdConfigureTab').closest('.ui-dialog').style.top = "50px";
    }
});

document.querySelectorAll('.coupleArray').forEach(element => {
    element.addEventListener('change', function() {
        let listValue = "";
        const nbValue = document.querySelectorAll('.coupleArray').length / 2;

        for (let pas = 0; pas < nbValue; pas++) {
            const arrayValue = document.getElementById('coupleArray' + pas).value;
            const keyValue = document.getElementById('coupleKey' + pas).value;
            listValue += arrayValue + '|' + keyValue + ';';
        }

        const changeListValue = document.getElementById('changeListValue');
        changeListValue.value = listValue.slice(0, -1);
        modifyWithoutSave = false;
    });
});

document.getElementById('div_displayCmdConfigure').setJeeValues(cmdInfo, '.cmdAttr');

document.querySelectorAll('.bt_testEnum').forEach(button => {
    button.addEventListener('click', function() {
        const dataValue = this.parentElement.querySelector('.data_value').value;

        domUtils.ajax({
            type: "POST",
            url: "plugins/lgthinq2/core/ajax/lgthinq2.ajax.php",
            data: {
                action: "testEnum",
                data_key: cmdInfo.configuration.key,
                data_value: dataValue,
                path: cmdInfo.configuration.path,
                eqLogic_id: cmdInfo.eqLogic_id
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) {
                if (data.state !== 'ok') {
                    jeedomUtils.showAlert({message: data.result, level: 'danger'});
                    return;
                }
                button.nextElementSibling.innerHTML = data;
            }
        });
    });
});

// Sauvegarde
document.getElementById('bt_cmdConfigureSave').addEventListener('click', function() {
    var cmd = document.getElementById('div_displayCmdConfigure').getJeeValues('.cmdAttr')[0]
    cmdInfo.configuration = {};
    cmdInfo.logicalId = cmd.logicalId;

    if (cmdInfo.type === 'info') {
        cmdInfo.configuration.default = cmd.configuration.default;
        cmdInfo.configuration.originalType = cmd.configuration.originalType;
    } else {
        cmdInfo.configuration.ctrlKey = cmd.configuration.ctrlKey;
        cmdInfo.configuration.cmd = cmd.configuration.cmd;
        cmdInfo.configuration.dataKey = cmd.configuration.dataKey;
        cmdInfo.configuration.dataValue = cmd.configuration.dataValue;

        if (cmdInfo.configuration.listValue && cmdInfo.configuration.listValue !== '') {
            cmdInfo.configuration.listValue = cmd.configuration.listValue;
        }
        if (cmd.configuration.dataSetList && cmd.configuration.dataSetList !== '') {
            cmdInfo.configuration.dataSetList = cmd.configuration.dataSetList;
        }
    }

    jeedom.cmd.save({
        cmd: cmdInfo,
        error: function(error) {
            jeedomUtils.showAlert({
                message: error.message,
                level: 'danger'
            }, '#md_displayCmdConfigure');
        },
        success: function(data) {
            modifyWithoutSave = false;
            jeedomUtils.showAlert({
                message: '{{Sauvegarde réussie}}',
                level: 'success'
            }, '#md_displayCmdConfigure');
        }
    });
});

</script>
