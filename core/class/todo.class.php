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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class todo extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */
	

    public static $_widgetPossibility = array('custom' => true);
	
	public static function changeTodo($action, $idcmd,$id) {
		
		switch ($action) {	
			case 'del': 
			    $todo = cmd::byId($idcmd);
                $todo->remove();
				break;
			case 'check':
				$todo = cmd::byId($idcmd);
				$todo->setIsVisible($id);
				$todo->save();
				break;
			default: 
			    $list = todo::byId($action);
				if (is_object($list)) {
					$todoCmd = new todoCmd();
					$todoCmd->setName(__(str_replace("'", " ",$idcmd), __FILE__));
					$todoCmd->setEqLogic_id($action);
					$todoCmd->setType('info');
					$todoCmd->setSubType('numeric');
					$todoCmd->save();	
					$list->refreshWidget();	
				}
				break;			
		}
	}
	
	public static function editCmd($id, $nom,$info,$datetodo,$timestamp) {
		$cmd= cmd::byId($id);
		$cmd->setName($nom);
		$cmd->setConfiguration('info', $info);
		$cmd->setConfiguration('cron_todo', $datetodo);
		$cmd->setConfiguration('timestamp', $timestamp);
		$cmd->save();
		
	}
	
    public static function getTodos() {
		 $return = array();
		 $i = 0;
		 $j = 0;
		 foreach (todo::byType('todo') as $todo) {
			 $id = $todo->getId();
				foreach (cmd::byEqLogicId($todo->getId()) as $cmd_todo){
					if ($cmd_todo->getName() != 'Creer') {
					$return[$i]['nom'] = $todo->getName();
					$return[$i]['id'] = $id;	
					$return[$i]['nom_cmd'][$j] = $cmd_todo->getName();
					$return[$i]['id_cmd'][$j] = $cmd_todo->getId();
					$return[$i]['timestamp'][$j] = $cmd_todo->getConfiguration('timestamp');
					if($cmd_todo->getIsVisible() == 1){
						$return[$i]['active'][$j] = 1;
					} else {
						$return[$i]['active'][$j] = 0;
					}
					$j++;
					}
				}
			$i++;
		 }
		 return $return;
	}


    public static function createTodo($nom,$todo) {
		$eqLogics = eqLogic::byType('todo');
	    foreach($eqLogics as $eqLogic) {
            if ($eqLogic->getName() == $nom) {
				$exist = 1;
				$id = $eqLogic->id;
				break;
			} else {
				$exist = 0;
			}
		};
		if ($exist == 1) {
			$todoCmd = new todoCmd();
			$todoCmd->setName(__(str_replace("'", " ",$todo), __FILE__));
			$todoCmd->setEqLogic_id($id);
			$todoCmd->setType('info');
			$todoCmd->setSubType('other');
			$todoCmd->save();	
		} else {
			$eqLogic = new eqLogic();
			$eqLogic->setEqType_name('todo');
			$eqLogic->setIsEnable(1);
			$eqLogic->setName($nom);
			$eqLogic->save();
			$eqLogic = self::byId($eqLogic->id);
			
			$todoCmd = new todoCmd();
			$todoCmd->setName(__(str_replace("'", " ",$todo), __FILE__));
			$todoCmd->setEqLogic_id($eqLogic->id);
			$todoCmd->setType('info');
			$todoCmd->setSubType('other');
			$todoCmd->save();
		}
	}
			


    /*     * *********************Méthodes d'instance************************* */
	
	

    public function preSave() {
		$creer = $this->getCmd(null, 'new');
		if (!is_object($creer)) {
			$creer = new todoCmd();
		}
		$creer->setName(__('New todo', __FILE__));
		$creer->setLogicalId('new');
		$creer->setEqLogic_id($this->id);
		$creer->setIsVisible(0);
		$creer->setType('action');
		$creer->setSubType('slider');
		$creer->save(); 		

	}

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

	
	public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$_version = jeedom::versionAlias($_version);
		$configuration = $this->getconfiguration();
		$id = $this->getId();
		$li = null;
		$test = array();
		$test = cmd::byEqLogicId($id);
		if ($_version != 'mobile') {
			foreach (cmd::byEqLogicId($id) as $cmd_todo){
				$array = array();
				$array = $cmd_todo ;
				$timestamp = $cmd_todo->getConfiguration('timestamp');
				if ($timestamp != '') {
					$now = time();
					if($now > $timestamp && $now < ($timestamp + 86400))  {
						$class = 'today';
					} elseif(($timestamp + 86400) > $now)  {
						$class = 'green';
					} else {
						$class = 'red';
					}
					
				} else {
					$class = '';
				}
				$cmd_id = $cmd_todo->getID();
				$name_event = $cmd_todo->getName();
				if ($name_event != 'Creer') {
					if($cmd_todo->getIsVisible() == 1){
					$li .= '<li id="'.$cmd_id.'" class="list-group-item list_edit" style="background-color:transparent;font-size : 0.9em;"><span><input value="'.$cmd_id.'" type="checkbox" >
		</span><span class="name_event_'.$cmd_id.'" name="'.$id.'" >'.$name_event.'</span> <div class="actions"><a  name="'.$id.'"  class="'.$class.'" alt="'.$cmd_id.'">info</a><a  name="'.$id.'"  class="edit" alt="'.$cmd_id.'">Edit</a><img src="plugins/todo/img/delete.png" href="" name="'.$id.'"  class="delete" alt="'.$cmd_id.'"></div> </li>';
					} else {
					$li .= '<li id="'.$cmd_id.'" class="list-group-item list_edit" style="text-decoration:line-through;background-color:transparent;font-size : 0.9em;"><span><input value="'.$cmd_id.'" type="checkbox" checked>
		</span><span class="name_event_'.$cmd_id.'" name="'.$id.'" >'.$name_event.' </span><div class="actions"><a  name="'.$id.'"  class="'.$class.'" alt="'.$cmd_id.'">info</a><a  name="'.$id.'"  class="edit" alt="'.$cmd_id.'">edit</a><img src="plugins/todo/img/delete.png" href="" name="'.$id.'"  class="delete" alt="'.$cmd_id.'"></div> </li>';
					}
				}
			}
		} else {
			foreach (cmd::byEqLogicId($id) as $cmd_todo){
				$conf_todo = $cmd_todo->getConfiguration();
				$cmd_id = $cmd_todo->getID();
				$timestamp = $cmd_todo->getConfiguration('timestamp');
				if ($timestamp != '') {
					$now = time();
					if($now > $timestamp && $now < ($timestamp + 86400))  {
						$class = 'today';
					} elseif(($timestamp + 86400) > $now)  {
						$class = 'green';
					} elseif ($now < $timestamp ) {
						$class = 'red';
					} else {
						$class = '';
					}
					
				}				
				$name_event = $cmd_todo->getName();
				if ($name_event != 'Creer') {
					if($cmd_todo->getIsVisible() == 1){
					$li .= '<li id="'.$cmd_id.'" class="list-group-item list_edit" style="background-color:transparent;font-size : 0.9em;"><span><input value="'.$cmd_id.'" type="checkbox" >
		</span><span class="name_mobile name_event_'.$cmd_id.'" name="'.$id.'" >'.$name_event.'</span> <div class="actions"><a  name="'.$id.'"  class="'.$class.'" alt="'.$cmd_id.'">info</a><img src="plugins/todo/img/delete.png" href="" name="'.$id.'"  class="delete" alt="'.$cmd_id.'"></div> </li>';
					} else {
					$li .= '<li id="'.$cmd_id.'" class="list-group-item list_edit" style="text-decoration:line-through;background-color:transparent;font-size : 0.9em;"><span><input value="'.$cmd_id.'" type="checkbox" checked>
		</span><span class="name_mobile name_event_'.$cmd_id.'" name="'.$id.'" >'.$name_event.' </span><div class="actions"><a  name="'.$id.'"  class="'.$class.'" alt="'.$cmd_id.'">info</a><img src="plugins/todo/img/delete.png" href="" name="'.$id.'"  class="delete" alt="'.$cmd_id.'"></div> </li>';
					}
				}
			}
		}


			$replace['#li#'] = $li;

			
		return template_replace($replace, getTemplate('core', $_version, 'todo','todo'));
				
	}
	
}

class todoCmd extends cmd {

    public function execute($_options = array()) {
		        
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>
