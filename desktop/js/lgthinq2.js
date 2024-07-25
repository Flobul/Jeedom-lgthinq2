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

/*var table = document.querySelector('#table_cmd .tablesorter tbody');
new Sortable(table, {
    animation: 150,
    draggable: ".cmd",
    handle: ".cmd",
    onStart: function(evt) {
        evt.item.classList.add('sortable-drag');
    },
    onEnd: function(evt) {
        evt.item.classList.remove('sortable-drag');
    }
});*/

function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = {
      configuration: {}
    };
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }

  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
  tr += '<td class="hidden-xs">'
  tr += '<span class="cmdAttr" data-l1key="id"></span>'
  tr += '</td>'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">'
  tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>'
  tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
  tr += '</div>'
  tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display:none;margin-top:5px;" title="{{Commande info liée}}">'
  tr += '<option value="">{{Aucune}}</option>'
  tr += '</select>'
  tr += '</td>'

  tr += '<td>';
  tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
  tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
  tr += '</td>';

  tr += '<td>'
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> '
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> '
  tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> '
  tr += '<div style="margin-top:7px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
  tr += '</div>'
  tr += '</td>'

  tr += '<td>';
  if (init(_cmd.type) == 'info') {
    tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
  }
  if (init(_cmd.subType) == 'select') {
    tr += '    <input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}">';
  }
  if (['select', 'slider', 'color'].includes(init(_cmd.subType)) || init(_cmd.configuration.updateLGCmdId) != '') {
    tr += '    <select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="updateLGCmdId" title="{{Commande d\'information à mettre à jour}}">';
    tr += '        <option value="">{{Aucune}}</option>';
    tr += '    </select>';
    tr += '    <input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="updateLGCmdToValue" placeholder="{{Valeur de l\'information}}">';
  }
  tr += '</td>';

  tr += '<td style="min-width:80px;width:200px;">';
  tr += '<div class="input-group">';
  if (is_numeric(_cmd.id) && _cmd.id != '') {
    tr += '<a class="btn btn-default btn-xs cmdAction roundedLeft" data-action="configure" title="{{Configuration de la commande}} ' + _cmd.type + '"><i class="fa fa-cogs"></i></a>';
    tr += '<a class="btn btn-warning btn-xs cmdAttr" data-action="configureCommand" title="{{Modification de la commande}} ' + _cmd.type + '"><i class="fas fa-wrench"></i></a>';
    tr += '<a class="btn btn-success btn-xs cmdAction" data-action="test" title="{{Tester}}"><i class="fa fa-rss"></i> {{Tester}}</a>';
  }
  tr += '<a class="btn btn-danger btn-xs cmdAction roundedRight" data-action="remove" title="{{Suppression de la commande}} ' + _cmd.type + '"><i class="fas fa-minus-circle"></i></a>';
  tr += '</tr>';


  let newRow = document.createElement('tr')
  newRow.innerHTML = tr
  newRow.addClass('cmd')
  newRow.setAttribute('data-cmd_id', init(_cmd.id))
  document.getElementById('table_cmd').querySelector('tbody').appendChild(newRow)

  jeedom.eqLogic.buildSelectCmd({
      id: document.querySelector('.eqLogicAttr[data-l1key="id"]').jeeValue(),
      filter: { type: 'info' },
      error: function(error) {
          jeedomUtils.showAlert({ message: error.message, level: 'danger' })
      },
      success: function(result) {
          newRow.querySelector('.cmdAttr[data-l1key="value"]').insertAdjacentHTML('beforeend', result)
          newRow.querySelector('.cmdAttr[data-l1key="configuration"][data-l2key="updateLGCmdId"]')?.insertAdjacentHTML('beforeend', result)
          newRow.setJeeValues(_cmd, '.cmdAttr')
          jeedom.cmd.changeType(newRow, init(_cmd.subType))
      }
  });
}

document.getElementById('div_lgthinq2').addEventListener('click', function(event) {
    var _target = null
    if (_target = event.target.closest('#bt_getCredentials')) {
        domUtils.ajax({
            type: "POST",
            url: "plugins/lgthinq2/core/ajax/lgthinq2.ajax.php",
            data: {
                action: "getCredentials"
            },
            dataType: 'json',
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
            }
        });
    }
    if (_target = event.target.closest('#bt_healthlgthinq2')) {
        jeeDialog.dialog({
            title: '{{Santé LG Thinq}}',
            contentUrl: 'index.php?v=d&plugin=lgthinq2&modal=health'
        });
    }
    if (_target = event.target.closest('#bt_synchronizelgthinq2')) {
        synchronize(false, false);
    }
    if (_target = event.target.closest('#bt_documentationlgthinq2')) {
        window.open(_target.getAttribute('data-location'), '_blank');
    }
    if (_target = event.target.closest('.cmdAttr[data-action=configureCommand]')) {
        jeeDialog.dialog({
            title: "{{Configuration de la commande}}",
            contentUrl: 'index.php?v=d&plugin=lgthinq2&modal=command.configure&id=' + _target.closest('.cmd').dataset.cmd_id
        });
    }
    if (_target = event.target.closest('#bt_autoDetectModule')) {
        var dialog_title = '{{Recharger la configuration}}';
        var dialog_message = '<form class="form-horizontal onsubmit="return false;"> ';
        dialog_message += '<label class="control-label" > {{Sélectionner le mode de rechargement de la configuration.}} </label> ' +
            '<div> <div class="radio"> <label > ' +
            '<input type="radio" name="command" id="command-0" value="0" checked="checked"> {{Sans recréer les commandes mais en créant les manquantes}} </label> ' +
            '</div><div class="radio"> <label > ' +
            '<input type="radio" name="command" id="command-1" value="1"> {{En recréant les commandes}}</label> ' +
            '</div> ' +
            '</div><br>' +
            '<label class="lbl lbl-warning" for="name">{{Attention, "en recréant les commandes" va supprimer les commandes existantes.}}</label> ';
        dialog_message += '</form>';
        var eqLogicId = document.querySelector('.eqLogicAttr[data-l1key=id]').value;
        var eqLogicDisplayCard = document.querySelector('.eqLogicDisplayCard[data-eqLogic_id="' + eqLogicId + '"]');
        jeeDialog.dialog({
            id: 'bbReloadConfigLG',
            title: dialog_title,
            message: dialog_message,
            width: '450px',
            buttons: {
                cancel: {
                    label: '{{Annuler}}',
                    className: "danger",
                    callback: {
                        click: function(event) {
                            jeeDialog.get('#bbReloadConfigLG').close();
                        }
                    }
                },
                confirm: {
                    label: "{{Recharger}}",
                    className: "success",
                    callback: {
                        click: function(event) {
                            if (document.querySelector("input[name='command']:checked").value === "1") {
                                jeeDialog.confirm('{{Êtes-vous sûr de vouloir récréer toutes les commandes ? Cela va supprimer les commandes existantes.}}', function(result) {
                                    if (result) {
                                        synchronize(document.querySelector('.eqLogicAttr[data-l1key=id]').value, true);
                                    }
                                });
                            } else {
                                synchronize(document.querySelector('.eqLogicAttr[data-l1key=id]').value, false);
                            }
                        }
                    }
                }
            },
            onClose: function() {
              jeeDialog.get('#bbReloadConfigLG').destroy()
            }
        });
    }
    if (_target = event.target.closest('.eqLogicAction[data-action=delete]')) {
        var what = _target.dataset.action2;
        if (what == 'appareils') var text = '{{Cette action supprimera les}} ' + what + ' {{retirés de LGThinq.}}';
        else if (what == 'all') var text = '{{Cette action supprimera tous les appareils.}}';
        jeeDialog.confirm(text, function(result) {
            if (result) {
                domUtils.ajax({
                    type: "POST",
                    dataType: 'json',
                    async: false,
                    data: {
                        action: "deleteEquipments",
                        what: what
                    },
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
                            message: '{{Suppression réussie}} : ' + what,
                            level: 'success'
                        });
                        location.reload();
                    }
                });
            }
        });
    }
});

function synchronize(_id = false, _deleteCmds = false) {
  jeedomUtils.showAlert({
    message: '{{Synchronisation en cours}}',
    level: 'warning'
  });

  document.querySelector('#bt_synchronizelgthinq2 > i.fas').classList.add('fa-spin');
  domUtils.ajax({
    type: "POST",
    url: "plugins/lgthinq2/core/ajax/lgthinq2.ajax.php",
    data: {
      action: "synchronize",
      id: _id,
      deleteCmds: _deleteCmds
    },
    async: true,
    dataType: 'json',
    error: function(request, status, error) {
      handleAjaxError(request, status, error);
    },
    success: function(data) {
      document.querySelector('#bt_synchronizelgthinq2 > i.fas').classList.remove('fa-spin');
      if (data.state != 'ok') {
        jeedomUtils.showAlert({
          message: data.result,
          level: 'danger'
        });
        return;
      } else if (data.result == false) {
        jeedomUtils.showAlert({
          message: '{{Veuillez renseigner un identifiant et un mot de passe de connexion.}}',
          level: 'danger'
        });
        return;
      } else {
        jeedomUtils.showAlert({
          message: '{{Synchronisation terminée}}',
          level: 'success'
        });
        document.querySelector('#bt_synchronizelgthinq2 > i.fas').classList.remove('fa-spin');
        window.location.reload();
      }
    }
  });
}

function printEqLogic(_eqLogic) {
  document.getElementById('nbTotalCmds').innerHTML = '<span class="label label-info">' + _eqLogic.cmd.length + '</span>';
  domUtils.ajax({
    type: "POST",
    url: "plugins/lgthinq2/core/ajax/lgthinq2.ajax.php",
    data: {
      action: "getImage",
      id: _eqLogic.id
    },
    dataType: 'json',
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
      if (data.result != '') {
         document.getElementById('img_device').setAttribute("src", data.result);
      }
    }
  })
}
