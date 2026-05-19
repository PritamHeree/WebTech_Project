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

