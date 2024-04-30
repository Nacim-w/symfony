<?php
declare(strict_types=1);

namespace Shake\Utils;

use Nette,
	Nette\Http\FileUpload,
	Nette\Utils\Strings,
	Nette\Utils\FileSystem;


/**
 * UploadTools
 *
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class UploadTools
{
	const IMAGE = '(jpe?g)|(png)|(gif)|(bmp)|(ico)';

	protected $wwwDir = NULL;


	public function __construct(string $wwwDir)
	{
		$this->wwwDir = $wwwDir;
	}


	public function upload(FileUpload $file, string $dir, string $filename = NULL)
	{
		$dir = trim($dir, '/\\');
		FileSystem::createDir("$this->wwwDir/$dir");

		$filename = $filename?: $file->name;
		$filename = $this->getSafeFilename($dir, $filename);

		$file->move("$this->wwwDir/$dir/$filename");

		return "/$dir/$filename";
	}


	public function checkFileType($file, $pattern)
	{
		$fileName = is_string($file)? $file: $file->name;

		return (preg_match("/\\.($pattern)$/i", $fileName) === 1);
	}


	public function getSafeFilename($dir, $name)
	{
		$dir = trim($dir, '/\\');

		// 1) Parse filename to name and extension
		$parts = explode('.', $name);

		if (count($parts) >= 2) {
			$extension = '.' . array_pop($parts);
			$name = join('.', $parts);
		} else {
			$extension = '';
			$name = $parts[0];
		}

		$name = Strings::webalize($name, '.');

		// 2) generate safe filename
		$return = $name . $extension;
		$i = 1;
		while (file_exists("$this->wwwDir/$dir/$return")) {
			$i++;
			$return = $name . "-$i" . $extension;
		}

		// 3) Return
		return $return;
	}


	public function splitFilename($fileName)
	{
		$parts = explode('.', $fileName);

		if (count($parts) >= 2) {
			$extension = '.' . array_pop($parts);
			$name = join('.', $parts);
		} else {
			$extension = '';
			$name = $parts[0];
		}

		return [$name, $extension];
	}

}
