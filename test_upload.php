<?php
$tmpName = '/tmp/dummy_image.jpg';
file_put_contents($tmpName, 'fake image data');
$_FILES['image'] = [
    'name' => 'dummy_image.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $tmpName,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($tmpName)
];
// test the image validation logic without a real HTTP upload
// note: move_uploaded_file will fail here because this is not a true upload stream.
$size = $_FILES['image']['size'];
$type = mime_content_type($tmpName);
echo "Type: $type\n";
if (!in_array($type, ['image/jpeg', 'image/png'])) {
    echo "Rejected type: $type\n";
} else {
    echo "Type accepted.\n";
}
$dir = 'public/uploads/menu';
$filename = uniqid() . '.jpg';
$destination = $dir . '/' . $filename;
echo "Moving $tmpName to $destination\n";
// move_uploaded_file won't work on fake uploads unless from actual HTTP POST.
// But we can check if is_uploaded_file would return false.
// Wait, the controller uses move_uploaded_file directly without checking is_uploaded_file.
