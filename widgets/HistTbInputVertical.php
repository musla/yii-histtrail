<?php
/**
 * HistTbInputVertical class file.
 * @author Tomas Hnilica <tomas@tomashnilica.com>
 * @copyright Copyright &copy; Tomas Hnilica 2013
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package histtrail.widgets
 * @depends on YiiBooster extension
 * 
 * TODO: refine the HTML output markup 
 */

Yii::import('bootstrap.widgets.input.TbInputVertical');

class HistTbInputVertical extends TbInputVertical {

	public $inputHtmlOptions = array();
	/**
	 * Renders a text field with the validity daterangepicker.
	 * @return string the rendered content
	 * Not sure wheter this is the right place 
	 */
	protected function textField()
	{
		echo CHtml::tag('div', $this->htmlOptions, false, false);
		echo $this->getLabel();
		echo $this->getPrepend();
		echo $this->form->textField($this->model, $this->attribute, $this->inputHtmlOptions);
		
		echo "<br>".Yii::t('app','Validity').":&nbsp;";
		echo $this->widget('bootstrap.widgets.TbDateRangePicker', 
				array('model'=>$this->model, 
					  'attribute'=>'validityRanges['.$this->attribute.']',
					  'options'=>array(
					  	'locale'=>array(
						     'applyLabel' => Yii::t('app', 'Apply'),
						     'clearLabel' => Yii::t('app', 'Clear'),
						     'fromLabel' => Yii::t('app', 'From'),
						     'toLabel' => Yii::t('app', 'To'),
						     'customRangeLabel' => Yii::t('app', 'Ranges'),
						),
						'format' => Yii::app()->locale->dateFormat,						
					  ),
					   
				), true);
			
		$unqId = "HistTrail_". get_class($this->model) ."_" . $this->attribute; 

		/*manual table creation, need to improve HTML markup */
		$hist = $this->model->getHistory($this->attribute);
		$h = '<table class="items table table-striped table-bordered table-condensed">';
		$h .= '<thead><th>'.Yii::t('app','Value').'</th><th>'.Yii::t('app','From').'</th><th>'.Yii::t('app','To').'</th></thead><tbody>';
		foreach($hist as $hr) $h.="<tr><td>".$hr['value']."</td>
					<td>".Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($hr['valid_from'], "yyyy-MM-dd"),"medium",null)."</td>
					<td>".Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($hr['valid_to'], "yyyy-MM-dd"),"medium",null)."</td></tr>";
		$h .= '</tbody></table>';
		
		//better solution but includes css and js 
		/*		 
		$hist = $this->model->getHistoryTrails($this->attribute);
		$gridDataProvider = new CArrayDataProvider($hist);
		$gridColumns = array(
			array('name'=>'new_value', 'header'=>Yii::t('app','Value'),),
			array('name'=>'valid_from', 'header'=>Yii::t('app','From'), 'value'=> 'Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($data->valid_from, "yyyy-MM-dd"),"medium",null)' ),
			array('name'=>'valid_to', 'header'=>Yii::t('app','To'), 'value'=> 'Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($data->valid_to, "yyyy-MM-dd"),"medium",null)'),
		);
		
		$h =  $this->widget('bootstrap.widgets.TbGridView', array(
		    'type' => 'striped bordered condensed',
		    'id'   => 'tbg' . $unqId,
		    'dataProvider' => $gridDataProvider,
		    'template' => "{items}",
		    'filter' => null,
		    'columns' => $gridColumns,
		    ), true);
		*/
		echo "<a href='#' onclick='\$(\"#${unqId}\").toggle();return false;'>&nbsp;".Yii::t('app','History')."</a>";
		echo "<div id='${unqId}' style='display:none;'>${h}</div>";
		echo $this->getAppend();
		echo $this->getError() . $this->getHint();
		echo "</div>";

	}


}
?>