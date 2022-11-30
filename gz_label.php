<?php //die(__FILE__);
class gz_label extends gz_tpl {
	public static $field_labels 	= [
		'login'	=> 'ชื่อล็อกอิน (Username)'
		,'password'	=> 'รหัสผ่าน (Password)'
		,'first_name'	=> 'ชื่อ (First name)'
		,'last_name'	=> 'นามสกุล (Last name)'
		,'sex'			=> 'เพศ (Sex)'
		,'date_of_birth'=> 'วันเกิด (Date of birth)'
		,'email'		=> 'อีเมล์ (Email)'
		,'tel_number'	=> 'โทรศัพท์ (Tel)'
		,'addr_1'		=> 'ที่อยู่ (Address)'
		,'addr_district'	=> 'ตำบล (District)'
		,'addr_amphoe'		=> 'อำเภอ (Amphoe)'
		,'addr_province'	=> 'จังหวัด (Province)'
		,'addr_zipcode'		=> 'รหัสไปรษณีย์ (Zipcode)'

		,'delivery'			=> 'การจัดส่ง / Delivery'
		,'jersey_color'		=> 'สีเสื้อ / Jersey color'
		,'jersey_size'		=> 'ขนาดเสื้อ / Jersey size'
		,'distance'			=> 'ระยะ / Distance'
		,'age_group'		=> 'กลุ่มอายุ / Age group'
		,'event_id'			=> 'รหัสงาน / Event ID'
		,'event_title'		=> 'ชื่องาน / Event name'

		,'emergency_contact'=> 'ผู้ติดต่อฉุกเฉิน / Emergency contact'
		,'emergency_tel'	=> 'เบอร์ติดต่อฉุกเฉิน / Emergency tel'
		,'team'			=> 'ทีม (team)'

		,'ref_id'		=> 'หมายเลขอ้างอิง / Reference #'
		,'regis_code' 	=> 'รหัสลงทะเบียน / Registration #'

		,'pay_status'	=> 'สถานะการจ่ายเงิน / Payment status' //is_paid
		,'pay_slip'		=> ['สลิป / Payment slip','_gallery_']

		,'pay_method'	=> 'วิธีการชำระค่าสมัคร / How to pay'
		,'note'			=> 'หมายเหตุ / Note'

		,'signup_date'	=> 'วันที่สมัคร'
	];
}
