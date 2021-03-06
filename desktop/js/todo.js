
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



/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
	if (_cmd.logicalId == 'getlist' || _cmd.logicalId == 'new' || _cmd.logicalId == 'list') {
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" style="display : none;" >';
		tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;"><input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 90%;margin-left:auto;margin-right:auto;" placeholder="{{Nom}}" /></td>';
		tr += '<td><span><input type="checkbox" data-size="mini" data-label-text="{{Visible}}" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/></span>'; 
		tr += '<span class="type" type="info" style="display : none;">' + jeedom.cmd.availableType() + '</span>';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="display : none;"></span>';
		tr += '</td>';
		tr += '<td><i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
		tr += '</td>';
		tr += '</tr>';		
		
	} else {
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" >';
		tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;"><input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 90%;margin-left:auto;margin-right:auto;" placeholder="{{Nom}}" /></td>';
		tr += '<td><input id="ident' + init(_cmd.id) + '" class="delai cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="cron_todo" style="width :90%;margin-left:auto;margin-right:auto;" placeholder="{{delai}}" readonly/><input type="hidden" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="timestamp" value=""  ></td>';
		tr += '<td><span><input type="checkbox" data-size="mini" data-label-text="{{Visible}}" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/></span>'; 
		tr += '<span class="type" type="info" style="display : none;">' + jeedom.cmd.availableType() + '</span>';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="display : none;"></span>';
		tr += '</td>';
		tr += '<td><i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
		tr += '</td>';
		tr += '</tr>';		
		

	}


    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));

		
	$( "input[id^='ident']" ).datepicker({
		  dateFormat: 'dd/mm/yy',
		  minDate: 0,	  
		  onClose: function( dateString ){
			  var newdate = dateString.split("/").reverse().join("-");
			  var myDate = new Date( newdate ).getTime() / 1000 ;
		   
			  console.log( 'newdate :' + newdate );
		   
			  $( this ).next().val( myDate );
		   
			  console.log( 'val :' + $( this ).next().val() );
		   }
	});			
}


