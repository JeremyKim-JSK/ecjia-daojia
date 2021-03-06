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
 * ECJIA 管理中心商店设置
 */
class admin_shop_config extends ecjia_admin {
	private $db;
	public function __construct() {
		parent::__construct();

		$this->db = RC_Loader::load_model('shop_config_model');

		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('bootstrap-placeholder');
		RC_Style::enqueue_style('bootstrap-toggle-buttons', RC_Uri::admin_url() . '/statics/lib/toggle_buttons/bootstrap-toggle-buttons.css', array('ecjia'));
		RC_Script::enqueue_script('jquery-toggle-buttons', RC_Uri::admin_url() . '/statics/lib/toggle_buttons/jquery.toggle.buttons.js', array('ecjia-admin'), false, 1);
		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('jquery-uniform');
		RC_Script::enqueue_script('smoke');
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('微商城设置', 'touch'), RC_Uri::url('touch/admin_shop_config/init')));
	}


	/**
	 * 列表编辑
	 */
	public function init() {
		$this->admin_priv('touch_shop_config');

		RC_Style::enqueue_style('chosen');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Script::enqueue_script('ecjia-region');
		RC_Script::enqueue_script('ecjia-shop_config', RC_Uri::admin_url() . '/statics/js/ecjia/ecjia-shop_config.js', array('ecjia-admin'), false, true);

		ecjia_screen::get_current_screen()->remove_last_nav_here();
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('微商城设置', 'touch')));

		/* 可选语言 */
		$dir = opendir(SITE_SYSTEM_PATH. 'languages');
		$lang_list = array();
		while (($file = readdir($dir)) != false) {
			if ($file != '.' && $file != '..' &&  $file != '.svn' && $file != '_svn' && is_dir(SITE_SYSTEM_PATH . 'languages/' .$file)) {
				$lang_list[] = $file;
			}
		}
		closedir($dir);
		$this->assign('lang_list',    $lang_list);

		if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'iis') !== false) {
			$shop_config_jslang = array(
				'rewrite_confirm' => __("URL Rewrite 功能要求您的 Web Server 必须安装IIS，并且起用了 ISAPI Rewrite 模块。如果您使用的是ISAPI Rewrite商业版，请您确认是否已经将httpd.txt文件重命名为httpd.ini。如果您使用的是ISAPI Rewrite免费版，请您确认是否已经将httpd.txt文件内的内容复制到ISAPI Rewrite安装目录中httpd.ini里。", 'touch'),
			);
		} else {
			$shop_config_jslang = array(
				'rewrite_confirm' => __("URL Rewrite 功能要求您的 Web Server 必须是 Apache，并且起用了 rewrite 模块。同时请您确认是否已经将htaccess.txt文件重命名为.htaccess。如果服务器上还有其他的重写规则请去掉注释,请将RewriteBase行的注释去掉,并将路径设置为服务器请求的绝对路径", 'touch'),
			);
		}
		RC_Script::localize_script( 'ecjia-shop_config', 'shop_config_lang', $shop_config_jslang );

		$this->assign('countries', with(new Ecjia\App\Setting\Country)->getCountries());
		if (ecjia::config('shop_country') > 0) {
			$this->assign('provinces', ecjia_region::getSubarea(ecjia::config('shop_country')));
			if (ecjia::config('shop_province')) {
				$this->assign('cities', ecjia_region::getSubarea(ecjia::config('shop_province')));
			}
		}

		$this->assign('ur_here',		__('微商城设置', 'touch'));
		$this->assign('cfg',			ecjia::config());
		$this->assign('group_list',		$this->get_settings(null, array('1', '2', '3', '4', '5', '6', '7', '8')));
		$this->assign('form_action',	RC_Uri::url('touch/admin_shop_config/update'));
		$this->assign_lang();

		$this->display('shop_config.dwt');
	}

	/**
	 * 商店设置表单提交处理
	 */
	public function update() {
		$this->admin_priv('touch_shop_config', ecjia::MSGTYPE_JSON);

		$arr  = array();
		$data = $this->db->field('id, value')->select();
		foreach ($data as $row) {
			$arr[$row['id']] = $row['value'];
		}
	  	foreach ($_POST['value'] AS $key => $val) {
			if($arr[$key] != $val){
				$data = array(
					'value' => trim($val),
				);
				$this->db->where(array('id'=>$key))->update($data);
			}
		}

		/* 处理上传文件 */
		$file_var_list = array();
		$data = $this->db->where(array('parent_id' => array('gt' => '0'), 'type' => 'file'))->select();
		foreach ($data as $row) {
			$file_var_list[$row['code']] = $row;
		}
		$disk = RC_Filesystem::disk();
		foreach ($_FILES AS $code => $file) {
			/* 判断用户是否选择了文件 */
			if ((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none')) {
				/*是否覆盖文件*/
				$replacefile = in_array($code, array('shop_logo','watermark','wap_logo','no_picture')) ? true : false;

				//删除原有文件
				if ($replacefile) {
					if ($disk->exists(RC_Upload::upload_path($file_var_list[$code]['value']))) {
						$disk->delete(RC_Upload::upload_path() . $file_var_list[$code]['value']);
					}
				}

				/*文件名命名*/
				$save_name = $code == 'icp_file' ? substr($file['name'],0, strrpos($file['name'], '.')) : $code;
				/*判断上传类型*/
				$extname = strtolower(substr($file['name'], strrpos($file['name'], '.') + 1));
				$filetype = $code == 'icp_file' ? (strrpos(RC_Config::load_config('system', 'UPLOAD_FILE_EXT'),$extname)? 'file' : 'image') :'image';
				$upload = RC_Upload::uploader($filetype, array('save_path' => 'data/assets/'.ecjia::config('template'),'save_name'=>$save_name,'replace'=>$replacefile,'auto_sub_dirs' => false));
				$image_info = $upload->upload($file);
				/* 判断是否上传成功 */
				if (!empty($image_info)) {
// 					$file_name = $image_info['savepath'].'/'.$image_info['savename'];
					$file_name = $upload->get_position($image_info);
					$data =  array(
						'value'  => $file_name
					);
					$this->db->where(array('code'=>$code))->update($data);
				} else {
					return $this->showmessage($upload->error(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
				}
			}
		}
		/* 处理发票类型及税率 */
		if (!empty($_POST['invoice_rate'])) {
			foreach ($_POST['invoice_rate'] as $key => $rate) {
				$rate = round(floatval($rate), 2);
				if ($rate < 0) {
					$rate = 0;
				}
				$_POST['invoice_rate'][$key] = $rate;
			}
			$invoice = array(
					'type'  => $_POST['invoice_type'],
					'rate'  => $_POST['invoice_rate']
			);
			$data  = array(
					'value' => serialize($invoice)
			);
			$this->db->where(array('code'=>'invoice_type'))->update($data);
		}

		/* 记录日志 */
		ecjia_admin::admin_log('', 'edit', 'shop_config');

		/* 清除缓存 */
		ecjia_config::instance()->clear_cache();
		ecjia_cloud::instance()->api('ecjia/record')->data(ecjia_utility::get_site_info())->run();

		$type = !empty($_POST['type']) ? $_POST['type'] : '';

		if ($type == 'mail_setting') {
			$message_info = __('邮件服务器设置成功。', 'touch');
		} else {
			$message_info = __('保存商店设置成功。', 'touch');
		}

		return $this->showmessage($message_info , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
	}

	/**
	 * 删除上传文件
	 */
	public function del() {
		$this->admin_priv('touch_shop_config', ecjia::MSGTYPE_JSON);

		$code     = trim($_GET['code']);
		$img_name = $this->db->where(array('code'=>$code))->get_field('value');
// 		@unlink(RC_Upload::upload_path() . $img_name);
		$disk = RC_Filesystem::disk();
		$disk->delete(RC_Upload::upload_path() . $img_name);
		
		$this->update_configure($code, '');
		ecjia_admin::admin_log('', 'edit', 'shop_config');

		return $this->showmessage(__('保存商店设置成功。', 'touch') , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('touch/admin_shop_config/init')));
	}

	/**
	 * 获得设置信息
	 *
	 * @param   array   $groups     需要获得的设置组
	 * @param   array   $excludes   不需要获得的设置组
	 *
	 * @return  array
	 */
	private function get_settings($groups=array(), $excludes=array()) {
		$config_groups = '';
		$excludes_groups = '';

		if (!empty($groups)) {
			foreach ($groups AS $key=>$val) {
				$config_groups .= " AND (id='$val' OR parent_id='$val')";
			}
		}

		if (!empty($excludes)) {
			foreach ($excludes AS $key=>$val) {
				$excludes_groups .= " AND (parent_id<>'$val' AND id<>'$val')";
			}
		}

		$item_list = $this->db->where('type <>"hidden"'.$config_groups . $excludes_groups)->order(array('parent_id' => 'asc', 'sort_order' => 'asc', 'id' => 'asc'))->select();

		/* 整理数据 */
		$group_list     = array();
		$cfg_name_lang  = config('app-touch::touch_config.cfg_name');
		$cfg_desc_lang  = config('app-touch::touch_config.cfg_desc');
		$cfg_range_lang = config('app-touch::touch_config.cfg_range');

		/* 增加图标数组 */
		$icon_arr = array(
			'shop_info'		=> 'fontello-icon-wrench',
			'basic'			=> 'fontello-icon-info',
			'display'		=> 'fontello-icon-desktop',
			'shopping_flow'	=> 'fontello-icon-truck',
			'goods'			=> 'fontello-icon-gift',
			'sms'			=> 'fontello-icon-chat-empty',
			'wap'			=> 'fontello-icon-tablet'
		);

		foreach ($item_list AS $key => $item) {
			$pid          = $item['parent_id'];
			$item['name'] = isset($cfg_name_lang[$item['code']]) ? $cfg_name_lang[$item['code']] : $item['code'];
			$item['desc'] = isset($cfg_desc_lang[$item['code']]) ? $cfg_desc_lang[$item['code']] : '';

			if ($item['type']=='file' && !empty($item['value'])) {
				if($item['code']=='icp_file') {
					$item['file_name'] = array_pop(explode('/', $item['value']));
				}
				$item['value'] = RC_Upload::upload_url() .'/'. $item['value'];
			}
			if ($item['code'] == 'sms_shop_mobile') {
				$item['url'] = 1;
			}
			if ($pid == 0) {
				/* 分组 */
				$item['icon'] = $icon_arr[$item['code']];
				if ($item['type'] == 'group') {
					$group_list[$item['id']] = $item;
				}
			} else {
				/* 变量 */
				if (isset($group_list[$pid])) {
					if ($item['store_range']) {
						$item['store_options'] = explode(',', $item['store_range']);

						foreach ($item['store_options'] AS $k => $v) {
							$item['display_options'][$k] = isset($cfg_range_lang[$item['code']][$v]) ?
							$cfg_range_lang[$item['code']][$v] : $v;
						}
					}
					$group_list[$pid]['vars'][] = $item;
				}
			}
		}
		return $group_list;
	}

    /**
     * 设置系统设置
     *
     * @param   string  $key
     * @param   string  $val
     *
     * @return  boolean
     */
	private function update_configure($key, $val='') {

		if (!empty($key)) {
			$data = array(
				'value' => $val
			);
			return $this->db->where(array('code'=>$key))->update($data);
		}
		return true;
	}
}

// end
