<?php
namespace Svg2Mpdf;

use Svg2Mpdf\Utils\File;
use Svg2Mpdf\Modules\Eraser;
use Svg2Mpdf\Modules\Cleaner;

final class Optimizer
{
    private File $file;

    private String $contents = '';

    public function __construct(string $filename)
    {
        $this->file = new File($filename);
        $this->contents = $this->file->read();
    }

    public function setContents(string $contents): Svg2Mpdf
    {
        $this->contents = $contents;

        return $this;
    }

    public function eraseComments(): Svg2Mpdf
    {
        Eraser::comments($this->contents);
        
        return $this;
    }

    public function cleanUp(): Svg2Mpdf
    {
        $cleaner = new Cleaner();
        $this->contents = $cleaner->do($this->contents);

        return $this;
    }

    public function replace(string $what, string $with): Svg2Mpdf
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