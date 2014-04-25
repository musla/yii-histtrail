<?php

class HistoryBehavior extends CActiveRecordBehavior{

	private $_oldattributes = array();

	public $allowed = array();
	public $ignored = array();

	public $dateFormat = 'Y-m-d H:i:s';
	public $userAttribute = null;

	public $storeTimestamp = false;
	public $skipNulls = true;
	
	
	public $validityRanges = array();
	public $oldValidityRanges = array();
	
	/**
	 * adjust setAttributes
	 * TH: this does not work for behaviors, have to do this manually in controllers ($model->validity)
	 */
	public function setAttributes($values,$safeOnly=true)
	{
			foreach($values as $key=>$val) if ($key == "validityRanges") {
				$this->validityRanges[$key] = $val;
			}
			return parent::setAttributes($values, $safeOnly);
	}
	
	/**
	 * Return attribute value to current date
	 * If not found, default model value is returned
	 * Date: yyy-mm-dd format 
	 */
	public function getHistoryValue($name, $date) {
		$trail = HistTrail::model()->findByAttributes(array('model'=>get_class($this->getOwner()),'model_id'=>$this->getNormalizedPk(), 'field'=>$name),
						new CDbCriteria(array('condition'=>' valid_from <= "'.$date.'" AND valid_to >= "'.$date.'" ', 'order'=>'stamp DESC', 'limit'=>'1',))
			  );
			  if ($trail) return $trail->new_value;
			  else return $this->getOwner()->getAttribute($name);
					
	}	
	 
	public function afterSave($event){
		$allowedFields = $this->allowed;
		$ignoredFields = $this->ignored;

		$newattributes = $this->getOwner()->getAttributes();
		$oldattributes = $this->getOldAttributes();

		// Lets unset fields which are not allowed
		if(sizeof($allowedFields) > 0){
			foreach($newattributes as $f => $v){
				if(array_search($f, $allowedFields) === false) unset($newattributes[$f]);
			}

			foreach($oldattributes as $f => $v){
				if(array_search($f, $allowedFields) === false) unset($oldattributes[$f]);
			}
		}

		// Lets unset fields which are ignored
		if(sizeof($ignoredFields) > 0){
			foreach($newattributes as $f => $v){
				if(array_search($f, $ignoredFields) !== false) unset($newattributes[$f]);
			}

			foreach($oldattributes as $f => $v){
				if(array_search($f, $ignoredFields) !== false) unset($oldattributes[$f]);
			}
		}
		// If no difference then WHY?
		// There is some kind of problem here that means "0" and 1 do not diff for array_diff so beware: stackoverflow.com/questions/12004231/php-array-diff-weirdness :S
		if(count(array_diff_assoc($newattributes, $oldattributes)) <= 0 && 
		   count(array_diff_assoc($this->oldValidityRanges, $this->validityRanges)) <= 0 ) return;

		// If this is a new record lets add a CREATE notification
		if ($this->getOwner()->getIsNewRecord())
			$this->leaveTrail('CREATE');

		// Now lets actually write the attributes
		$this->auditAttributes($newattributes, $oldattributes);
		return parent::afterSave($event);
	}

	public function auditAttributes($newattributes, $oldattributes = array()){

		foreach ($newattributes as $name => $value) {
			$old = isset($oldattributes[$name]) ? $oldattributes[$name] : '';

			// If we are skipping nulls then lets see if both sides are null
			if($this->skipNulls && empty($old) && empty($value)){
				continue;
			}
			$diffValidity = false;
			if (isset($this->validityRanges[$name]) && 
				 @$this->oldValidityRanges[$name] != $this->validityRanges[$name]
				) $diffValidity = true;
			// If they are not the same lets write an audit log
			if ($value != $old || $diffValidity) {
				$this->leaveTrail($this->getOwner()->getIsNewRecord() ? 'SET' : 'CHANGE', $name, $value, $old);
			}
		}
	}

	public function afterDelete($event){
		$this->leaveTrail('DELETE');
		return parent::afterDelete($event);
	}

	public function afterFind($event){
		$this->setOldAttributes($this->Owner->getAttributes());
		$this->setLastValidities();
		return parent::afterFind($event);
	}

	public function getOldAttributes(){
		return $this->_oldattributes;
	}

	public function setOldAttributes($value){
		$this->_oldattributes=$value;
	}
	
	/**
	 * Query for last used validities
	 */
	public function setLastValidities() {
		foreach($this->allowed as $name) {
			$trail = HistTrail::model()->findByAttributes(array('model'=>get_class($this->getOwner()),'model_id'=>$this->getNormalizedPk(), 'field'=>$name),
						new CDbCriteria(array('order'=>'stamp DESC', 'limit'=>'1',))
			  );
			  if ($trail) {
			  	$from = Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($trail->valid_from, 'yyyy-MM-dd'),'medium',null);
			  	$to = Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($trail->valid_to, 'yyyy-MM-dd'),'medium',null);
			  	$this->validityRanges[$name] = $from . " - " . $to;
			  	$this->oldValidityRanges[$name] = $from . " - " . $to;
			  } else {
			  	$this->validityRanges[$name] = '';
			  	$this->oldValidityRanges[$name] = '';			  
			  }
			
			
			
			
		}
	}
	
	/**
	 * Return array of history trails for a fields. 
	 */
	public function getHistoryTrails($name) {
		$trails = HistTrail::model()->findAllByAttributes(array('model'=>get_class($this->getOwner()),'model_id'=>$this->getNormalizedPk(), 'field'=>$name),
						new CDbCriteria(array('order'=>'stamp DESC'))
					);
		return $trails;			
	}

	/**
	 * Return array of changes for a field
	 */
	public function getHistory($name) {
		$trails = $this->getHistoryTrails($name);
		$ret = array();
		foreach($trails as $t) array_push($ret, array('value'=>$t->new_value, 'valid_from'=>$t->valid_from, 'valid_to'=>$t->valid_to));
		return $ret;
	}


	public function leaveTrail($action, $name = null, $value = null, $old_value = null){
		
		$log			= new HistTrail();
		$log->old_value = $old_value;
		$log->new_value = $value;
		$log->action	= $action;
		$log->model		= get_class($this->getOwner()); // Gets a plain text version of the model name
		$log->model_id	= $this->getNormalizedPk();
		$log->field		= $name;
		$log->stamp		= $this->storeTimestamp ? $time() : date($this->dateFormat); // If we are storing a timestamp lets get one else lets get the date
		$log->user_id	= $this->getUserId(); // Lets get the user id
		$log->valid_from = null;
		$log->valid_to = null;
		if (isset($this->validityRanges[$name])) {
			$validity = explode(" - ",$this->validityRanges[$name]);
			if (sizeof($validity)==2) {			
				$ts = CDateTimeParser::parse($validity[0], Yii::app()->locale->dateFormat);
            	$log->valid_from = date('Y-m-d', $ts);
				$ts = CDateTimeParser::parse($validity[1], Yii::app()->locale->dateFormat);
				$log->valid_to = date('Y-m-d', $ts);
			}
		}


		return $log->save();
	}

	public function getUserId(){
		if(isset($this->userAttribute)){
			$data = $this->getOwner()->getAttributes();
			return isset($data[$this->userAttribute]) ? $data[$this->userAttribute] : null;
		}else{
			try {
				$userid = Yii::app()->user->id;
				return empty($userid) ? null : $userid;
			} catch(Exception $e) { //If we have no user object, this must be a command line program
				return null;
			}
		}
	}

	protected function getNormalizedPk(){
		$pk = $this->getOwner()->getPrimaryKey();
		return is_array($pk) ? json_encode($pk) : $pk;
	}
}