<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 掌柜查看配送员详情
 * @author zrl
 */
class admin_shopkeeper_express_staff_detail_module extends api_admin implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {	
    	$this->authadminSession();
    	if ($_SESSION['staff_id'] <= 0) {
            return new ecjia_error(100, 'Invalid session');
        }
        //权限判断，查看配送员详情的权限
        $result = $this->admin_priv('mh_express_manage');
        if (is_ecjia_error($result)) {
        	return $result;
        }

    	$staff_id = $this->requestData('staff_id');
    	$size     = $this->requestData('pagination.count', 15);
		$page     = $this->requestData('pagination.page', 1);
    	
    	if (empty($staff_id)) {
    		return new ecjia_error( 'invalid_parameter', __('参数无效', 'express'));
    	}
    	$express_user_dbview = RC_DB::table('staff_user')->leftJoin('express_user', 'staff_user.user_id', '=', 'express_user.user_id');
    	$express_user_info =  $express_user_dbview->select('staff_user.*', 'express_user.longitude', 'express_user.latitude')->where('staff_user.user_id', '=', $staff_id)->first();
    	
    	if (empty($express_user_info)) {
    		return new ecjia_error('express_user_not_exists', '配送员信息不存在！');
    	}
    	
    	if ($express_user_info['online_status'] == '1') {
    		$online_status 			= 'online';
    		$label_online_status 	= '在线';
    	} elseif ($express_user_info['online_status'] == '4') {
    		$online_status 			= 'offline';
    		$label_online_status 	= '离线';
    	}
    	
    	$assign_count = RC_DB::table('express_order')->where('staff_id', $staff_id)->where('from', 'assign')->count();
    	$unfinished_count = RC_DB::table('express_order')->where('staff_id', $staff_id)->whereIn('status', array(1,2))->count();
    	$finished_count = RC_DB::table('express_order')->where('staff_id', $staff_id)->where('status', 5)->count();
    	
    	$pra = array(
    			'page' 		=> $page,
    			'size' 		=> $size,
    			'staff_id' 	=> $staff_id
    	);
    	
    	$express_order_list =  RC_Api::api('express', 'express_order_list', $pra);
    	
    	
    	
    	if (is_ecjia_error($express_order_list)) {
    		return $express_order_list;
    	}
    	//配送记录列表
    	$express_order_list_new = array();
		if (!empty($express_order_list['list'])) {
			foreach ($express_order_list['list'] as $res) {
				$express_order_list_new[] = array(
						'express_id' 			=> intval($res['express_id']),
						'express_sn' 			=> $res['express_sn'],
						'express_to_address' 	=> ecjia_region::getRegionName($res['district']).ecjia_region::getRegionName($res['street']).$res['address'],
						'formated_pickup_time'	=> RC_Time::local_date(ecjia::config('time_format'), $res['receive_time']),
						'formated_shipping_fee' => price_format($res['shipping_fee']),
						'express_status'		=> $res['express_status'],
						'label_express_status'	=> $res['label_express_status'],
				);
			}
		} 
		
    	$express_userinfo = array();
    	$express_userinfo = array(
    			'store_id' 				=> intval($express_user_info['store_id']),
    			'staff_id' 				=> intval($express_user_info['user_id']),
    			'avatar_img' 			=> !empty($express_user_info['avatar']) ?  RC_Upload::upload_url($express_user_info['avatar']) : '',
    			'staff_name'			=> $express_user_info['name'],
    			'nickname'				=> empty($express_user_info['nickname']) ? '' : $express_user_info['nickname'],
    			'user_ident'			=> empty($express_user_info['user_ident']) ? '' : $express_user_info['user_ident'],
    			'introduction'			=> empty($express_user_info['todolist']) ? '' : $express_user_info['todolist'],
    			'mobile'  				=> $express_user_info['mobile'],
    			'email'  				=> $express_user_info['email'],
    			'online_status' 		=> $online_status,
    			'label_online_status'	=> $label_online_status,
    			'express_user_location' => array('longitude' => $express_user_info['longitude'], 'latitude' => $express_user_info['latitude']),
    			'assign_count'			=> $assign_count,
    			'unfinished_count'		=> $unfinished_count,
    			'finished_count'		=> $finished_count,
    			'express_list'			=> $express_order_list_new,
    	);
    	
		return array('data' => $express_userinfo, 'pager' => $express_order_list['page']);
	 }	
}

// end