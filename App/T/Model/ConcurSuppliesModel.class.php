<?php
namespace T\Model;
use Think\Model;
class ConcurSuppliesModel extends Model{
	/**
	 * 获得某一求助项目所求物资
	 * @author zhu
	 */
	public function concurS($id){
		$res="";
		$need="";
		$obtain="";
		$prefix =  C('DB_PREFIX');
		$tb1=$prefix.'concur';
		$tb2=$prefix.'concur_supplies';
		$w=array(
			$tb2.'.concur_id'=>$id
		);
		$data=$this->field("$tb1.title,$tb1.money,$tb1.is_supplies,$tb1.is_service,$tb1.type,$tb2.id,$tb2.concur_id,$tb2.name,$tb2.num")->join("left join $tb1 on $tb1.id=$tb2.concur_id")->where($w)->select();
		if($data){
			foreach ($data as $key=>$vol){
				$r=D("T/ConcurSuppliesApply")->getConSup($vol['id'],$vol['concur_id']);
				$data[$key]['yNum']=0;
				if($r){
					foreach ($r as $k=>$v){
						$data[$key]['yNum'] +=$v['num'];
					}
				}
				$data[$key][noNum]=$vol['num']-$data[$key]['yNum'];
				$res .=$vol['name']."、";
				if($data[$key]['noNum']>0){
					$need .=$data[$key]['noNum']." x ".$vol['name']."、";
				}
				if($data[$key]['yNum']>0){
					$obtain .=$data[$key]['yNum']." x ".$vol['name']."、";
				}
				if($vol['num'] < $data[$key]['yNum']){
					$data[$key]['percent']="100%";
				}else{
					$data[$key]['percent']=(($data[$key]['yNum']/$vol['num'])*100)."%";
				}
			}
			$data[0]['res']=rtrim($res,"、");
			$data[0]['need']=rtrim($need,"、");
			$data[0]['obtain']=rtrim($obtain,"、");
		}
		return $data;
	}
        
        /**
         * 根据ID获取需要募捐或打算捐助的物资
         * @author liuzm
         */
        public function getSuppliesById($id){
            return $this->where('concur_id=%d',$id)->select();
        }
        
        /**
         * 根据apply_id 序列，获取当前登录用户的所有请求
         */
        public function getInfoByIdList($list){
            $prefix =  C('DB_PREFIX');
            $applyTab = $prefix.'concur_supplies_apply_details';
            $suppliesTab = $prefix.'concur_supplies';
            $sql = "select $applyTab.apply_id,$applyTab.supplies_id,$applyTab.num,$suppliesTab.name from $applyTab "
                    . " left join $suppliesTab on $applyTab.supplies_id = $suppliesTab.id"
                    . " where $applyTab.apply_id in ($list)";
            $result = $this->query($sql);
            $rinfo = array();
            foreach($result as $k=>$v){
	    	if($rinfo[$v['apply_id']]){
                	$rinfo[$v['apply_id']] .= $v['num'].' X '.$v['name'].'、';
		}else{
			$rinfo[$v['apply_id']] = $v['num'].' X '.$v['name'].'、';
		}
            }
	    return preg_replace('/、$/', "", $rinfo);;
        }
        
}