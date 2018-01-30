<?php

namespace Divart\Filemanager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class Filemanager
{
    function __construct()
    {
        $this->root = '../';
        $this->filemanager_path = env('FILEMANAGER_LOCATION', 'myfilemanager');
        $this->filemanager = $this->root.$this->filemanager_path.'/';
    }

    public function pathFilemanager()
    {
        return $this->filemanager;
    }

    public function replaceAddress($folder)
    {
        return $folder = str_replace("-", "/", $folder);
    }

    public function createFileManager()
    {
        if ( !is_dir($this->filemanager)) {
            return mkdir($this->filemanager, 0755);
        }
    }

    public function createFolder($path, $filename)
    {
        $path = $this->replaceAddress($path);
        if ( !is_dir($this->filemanager.$path.'/'.$filename)) {
            mkdir($this->filemanager.$path.'/'.$filename, 0777);
        }
        return $path;
    }

    public function renameFolder($path, $lastname, $name)
    {
        $path = $this->replaceAddress($path);
        if ( is_dir($this->filemanager.$path.'/'.$lastname)) {
            rename($this->filemanager.$path.'/'.$lastname, $this->filemanager.$path.'/'.$name);
        }
        return $path;
    }

    public function deleteFolder($path, $name)
    {
        $path = $this->replaceAddress($path);
        if ( is_dir($this->filemanager.$path.'/'.$name)) {
            rmdir($this->filemanager.$path.'/'.$name);
        }
        return $path;
    }

    public function initFolder($folder = '')
    {
        $folder = $this->replaceAddress($folder);
        if ( !empty($folder)) $this->filemanager .= $folder.'/';
        $this->createFileManager();

        $mass = array_except(scandir($this->filemanager), [0,1]);
        foreach ($mass as $key => $value) {

            if ( is_dir($this->filemanager.$value)) {
                $mass = array(
                    'name' => $value,
                    'type' => 'folder',
                    'address' => $this->address($this->filemanager.$value),
                    'full_address' => $this->filemanager.$value,
                    'size' => $this->dirSize($this->filemanager.$value),
                    'time' => filemtime($this->filemanager.$value)
                    );
                $derictory['folder'][] = $mass;
            } else {
                $mass = array(
                    'name' => $value,
                    'type' => 'file',
                    'address' => $this->address($this->filemanager.$value),
                    'full_address' => $this->filemanager.$value,
                    'size' => filesize($this->filemanager.$value),
                    'time' => filemtime($this->filemanager.$value)
                    );
                $derictory['files'][] = $mass;
            }
        }

        $derictory['info']['this_path'] = $folder;
        return $derictory;
    }

    public function fileDislocate($filename, $from, $to)
    {
        $from .= ($from != 'filemanager') ? '/' : '';
        if ( copy($this->filemanager.$from.$filename, $this->filemanager.$to.$filename)) {
            return unlink($this->filemanager.$path_from.$name);
        }
    }

    public function my_copy_all($from, $to, $rewrite = true)
    {
        if (is_dir($from)) {
            @mkdir($to);
            $d = dir($from);
            while (false !== ($entry = $d->read())) {

                if ($entry == "." || $entry == "..") continue;
                $this->my_copy_all($from.'/'.$entry, $to.'/'.$entry, $rewrite);
                unlink($from.'/'.$entry);
            }
            $d->close();
        } else {
            if ( !file_exists($to) || $rewrite)
            copy($from, $to);
        }
    }

    public function removeDirectory($dir)
    {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    public function getFile($path, $name)
    {   
        $img_format = array('jpeg', 'jpg', 'png');
        if ( file_exists($this->filemanager.$name)) {
            $file = array(
                'name' => $name,
                'address' => $this->address($this->filemanager),
                'full_address' => $this->filemanager,
                'size' => filesize($this->filemanager.$name),
                'time' => filemtime($this->filemanager.$name)
                );
            $file_extension = File::extension($this->filemanager.$name);
            if ( in_array($file_extension, $img_format)) {
                $file['type'] = 'image';
            } else {
                $file['type'] = 'text';
                $file['content'] = file($this->filemanager.$name);
            }
            return $file;
        }
    }

    public function createFile($path, $name, $data)
    {   
        $path = $this->replaceAddress($path);
        if ( !file_exists($this->filemanager.$path.'/'.$name)) {
            file_put_contents($this->filemanager.$path.'/'.$name, $data);
        }
        return $path;
    }

    public function uploadFile($path, $name, $data)
    {   
        $path = $this->replaceAddress($path);
        file_put_contents($this->filemanager.$data['name'], $data);
        return $path;
    }

    public function renameFile($path, $name, $newname)
    {
        $path = $this->replaceAddress($path);
        if ( file_exists($this->filemanager.$path.'/'.$name)) {
            rename($this->filemanager.$path.'/'.$name, $this->filemanager.$path.'/'.$newname);
        }
        return $path;
    }

    public function deleteFile($path, $filename)
    {
        $path = $this->replaceAddress($path);
        if (file_exists($this->filemanager.$path.'/'.$filename)) unlink($this->filemanager.$path.'/'.$filename);
        return $path;
    }

    public function dirSize($dir)
    {
        $totalsize = 0;
        if ($dirstream = @opendir($dir)) {
            while ( false !== ($filename = readdir($dirstream))) {
                if ($filename != "." && $filename != "..") {
                    if (is_file($dir."/".$filename)) $totalsize+=filesize($dir."/".$filename);
                    if (is_dir($dir."/".$filename)) $totalsize+=$this->dirSize($dir."/".$filename);
                }
            }
        }
        closedir($dirstream);
        return $totalsize;
    }

    public function address($full_address)
    {
        return mb_strimwidth($full_address, strlen($this->root.$this->filemanager_path), strlen($full_address));
    }

    public function sort($data, $value = 'time', $type = SORT_ASC)
    {
        $data_sort_file = array();
        foreach ($data['files'] as $key => $arr) {
            $data_sort_file[$key] = $arr[$value];
        }

        $data_sort_folder = array();
        foreach ($data['folder'] as $key => $arr) {
            $data_sort_folder[$key] = $arr[$value];
        }
        array_multisort($data_sort_file, $type, $data['files']);
        array_multisort($data_sort_folder, $type, $data['folder']);

        return $data;
    }

}