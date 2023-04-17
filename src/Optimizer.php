<?php
namespace Svg4Mpdf;

use Svg4Mpdf\Utils\File;
use Svg4Mpdf\Modules\Eraser;
use Svg4Mpdf\Modules\Cleaner;

final class Optimizer
{
    private File $file;

    private String $contents = '';

    public function __construct(string $filename)
    {
        $this->file = new File($filename);
        $this->contents = $this->file->read();
    }

    public function setContents(string $contents): Svg4Mpdf
    {
        $this->contents = $contents;

        return $this;
    }

    public function eraseComments(): Svg4Mpdf
    {
        Eraser::comments($this->contents);
        
        return $this;
    }

    public function cleanUp(): Svg4Mpdf
    {
        $cleaner = new Cleaner();
        $this->contents = $cleaner->do($this->contents);

        return $this;
    }

    public function replace(string $what, string $with): Svg4Mpdf
    {
        $this->contents = str_replace($what, $with, $this->contents);
    }

    public function getContents(): string 
    {
        return $this->contents;
    }

    public function showContents(): string 
    {
        header( 'Content-type: image/svg+xml' );
        die($this->contents);
    }

    public function save()
    {
        $this->file->write($this->contents);
    }

    // @todo: saveas
}