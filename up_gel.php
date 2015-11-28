<?php
$result = [
	'success' => false,
	'filename' => '',
];
//$filename = date('Y_m_d_H_i_s_') . mt_rand(0, 10000000);
$filename = $_POST['tr'] . '_' . $_POST['tg'] . '_' . $_POST['tb'] . '_' . $_POST['br'] . '_' . $_POST['bg'] . '_' . $_POST['bb'];
$path = 'gel_images/' . $filename . '.png';
if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
	if (move_uploaded_file($_FILES["image"]["tmp_name"], $path)) {
		$result['success'] = true;
		$result['filename'] = $filename;
	}
}
echo json_encode($result);
