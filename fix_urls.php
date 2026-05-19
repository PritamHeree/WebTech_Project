<?php
$dir = __DIR__;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() !== 'php') continue;
    if ($file->getFilename() === 'index.php') continue;
    if ($file->getFilename() === 'fix_urls.php') continue;

    $content = file_get_contents($file->getPathname());
    $original = $content;

    if (strpos($file->getPathname(), '/views/') !== false) {
        $content = preg_replace('#href="/WebTech%20Project/(.*?)"#', 'href="<?php echo url(\'/$1\'); ' . '?>"', $content);
        $content = preg_replace('#action="/WebTech%20Project/(.*?)"#', 'action="<?php echo url(\'/$1\'); ' . '?>"', $content);
        $content = preg_replace('#src="/WebTech%20Project/(.*?)"#', 'src="<?php echo url(\'/$1\'); ' . '?>"', $content);
        
        $content = str_replace('href="/WebTech%20Project"', 'href="<?php echo url(\'/\'); ' . '?>"', $content);
        $content = str_replace('action="/WebTech%20Project"', 'action="<?php echo url(\'/\'); ' . '?>"', $content);
    }

    if (strpos($file->getPathname(), '/controllers/') !== false) {
        $content = preg_replace('/redirect\([\'"]\/WebTech%20Project\/(.*?)[\'"]\)/', "redirect('/$1')", $content);
        $content = preg_replace('/redirect\([\'"]\/WebTech%20Project[\'"]\)/', "redirect('/')", $content);
    }
    
    if ($content !== $original) {
        file_put_contents($file->getPathname(), $content);
        echo "Updated: " . $file->getPathname() . "\n";
    }
}
echo "Done.\n";
