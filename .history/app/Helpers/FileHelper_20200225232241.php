<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Common\FtpServiceResponse;
use App\Common\FtpServiceResponseStatus;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Model;

class FileHelper
{
	/**
	 * Delete an old file and add a new file
	 * Check the code & describe property of FtpServiceResponse result to know what happens if it fails
	 * Failed cases: NOT_SAME_FILE_EXTENSION, FAILED, FILE_TYPE_NOT_SUPPORTED, FILE_NOT_RECEIVED.
	 * 
	 * @param string $diskName
	 * @param string $oldFileName
	 * @param UploadedFile|null $newFile
	 * @return FtpServiceResponse
	 */
	public static function change(string $diskName, ?UploadedFile $newFile, string $oldFileName): FtpServiceResponse
	{
		if (self::exist($diskName, $oldFileName)) {
			$response = self::delete($diskName, $oldFileName);

			if ($response->code === FtpServiceResponseStatus::SUCCESSFUL) {
				$response = self::store($diskName, $newFile, null, $oldFileName);
			}
		} else {
			$response = self::store($diskName, $newFile, null, $oldFileName);
		}

		return $response;
	}


	/**
	 * Check the code & describe property of FtpServiceResponse result to know what happens if it fails
	 * Failed cases: FAILED, FILE_NOT_EXIST.
	 * 
	 * @param string $diskName
	 * @param string|null $fileName
	 * @return FtpServiceResponse
	 */
	public static function delete(string $diskName, ?string $fileName): FtpServiceResponse
	{
		$response = new FtpServiceResponse(FtpServiceResponseStatus::SUCCESSFUL);

		if ($fileName && Storage::disk($diskName)->exists($fileName)) {
			$response->code = Storage::disk($diskName)->delete($fileName) ? FtpServiceResponseStatus::SUCCESSFUL : FtpServiceResponseStatus::FAILED;
			if ($response->code === FtpServiceResponseStatus::FAILED) {
				$thisFile = __FILE__;
				$thisLine = __LINE__;
				$response->describe = "Unable to delete file '{$fileName}' at {$thisFile} (line {$thisLine})";
			}
		} else {
			$response->code = FtpServiceResponseStatus::FILE_NOT_EXIST;
		}

		return $response;
	}


	/**
	 * Return null and 404 not found if the file not exist
	 *
	 * @param string $diskName
	 * @param string|null $fileName
	 * @param string $defaultFileName
	 * @return BinaryFileResponse|null
	 */
	public static function download(string $diskName, ?string $fileName, string $defaultFileName = null): ?BinaryFileResponse
	{
		$result = null;

		if ($fileName && Storage::disk($diskName)->exists($fileName)) {
			$result = response()->download(Storage::disk($diskName)->path($fileName));
		} else if ($defaultFileName && Storage::disk($diskName)->exists($defaultFileName)) {
			$result = response()->download(Storage::disk($diskName)->path($defaultFileName));
		} else {
			http_response_code(404);
		}

		return $result;
	}


	/**
	 * Save file and return the file name if successFUL otherwise return null to FtpServiceResponse's result
	 * Check the code & describe property of FtpServiceResponse result to know what happens if it fails
	 * Failed cases: NOT_SAME_FILE_EXTENSION, FAILED, FILE_TYPE_NOT_SUPPORTED, FILE_NOT_RECEIVED.
	 * 
	 * @param string $diskName
	 * @param UploadedFile|null $file
	 * @param array|null $fileExtensions
	 * @param string $fileName
	 * @return FtpServiceResponse
	 */
	public static function store(string $diskName, ?UploadedFile $file, ?array $fileExtensions = null, string $fileName = null): FtpServiceResponse
	{
		$response = new FtpServiceResponse(FtpServiceResponseStatus::SUCCESSFUL);

		if (self::checkExistUploadingFile($file)) {
			if (self::checkFileExtension($file, $fileExtensions)) {
				if ($fileName == null) {
					$result = $file->store('', $diskName);
					if ($result) {
						$response->data = $result;
					} else {
						$response->code = FtpServiceResponseStatus::STORAGE_FAILED;
					}
				} else {
					//check if uploaded file extension not match to $fileName extension
					if (substr($fileName, strrpos($fileName, '.') + 1) === $file->extension()) {
						$result = $file->storeAs('', $fileName, $diskName);
						if ($result) {
							$response->data = $result;
						} else {
							$response->code = FtpServiceResponseStatus::STORAGE_FAILED;
						}
					} else {
						$response->code = FtpServiceResponseStatus::NOT_SAME_FILE_EXTENSION;
					}
				}
			} else {
				$response->describe = "File extension ({$file->extension()}) is not supported!";
				$response->code = FtpServiceResponseStatus::FILE_TYPE_NOT_SUPPORTED;
			}
		} else {
			$response->describe = 'Do not receive any files! Check upload_max_filesize on PHP!';
			$response->code = FtpServiceResponseStatus::FILE_NOT_RECEIVED;
		}

		return $response;
	}


	/**
	 * Save uploaded file and update the file name to the Model's field
	 * Check the code & describe property of FtpServiceResponse result to know what happens if it fails
	 * Failed cases: FAILED, FILE_TYPE_NOT_SUPPORTED, FILE_NOT_RECEIVED.
	 *
	 * @param string $diskName
	 * @param UploadedFile|null $file
	 * @param Model $model
	 * @param string $fieldName
	 * @param array|null $fileExtensions
	 * @return FtpServiceResponse
	 */
	public static function storeForModel(string $diskName, ?UploadedFile $file, Model $model, string $fieldName, ?array $fileExtensions = null): FtpServiceResponse
	{
		$response = self::store($diskName, $file, $fileExtensions);
		if ($response->code === FtpServiceResponseStatus::SUCCESSFUL) {
			$model->$fieldName = $response->data;
			if (!$model->save()) {
				$response->code = FtpServiceResponseStatus::FAILED;
				$response->describe = 'Unable to save the ' . get_class($model);
			}
		}

		return $response;
	}


	/**
	 * @return bool
	 */
	public static function exist(string $diskName, string $fileName): bool
	{
		return Storage::disk($diskName)->exists($fileName);
	}


	/**
	 * @param UploadedFile|null $file
	 * @return bool
	 */
	private static function checkExistUploadingFile(?UploadedFile $file): bool
	{
		return $file != null && gettype($file) === 'object' && get_class($file) === "Illuminate\Http\UploadedFile" && $file->getSize() > 0;
	}


	/**
	 * @param UploadedFile $file
	 * @param array|null $fileExtensions
	 * @return bool
	 */
	private static function checkFileExtension(UploadedFile $file, ?array $fileExtensions = null): bool
	{
		if ($fileExtensions == null) {
			return true;
		}

		$result = false;
		$checkedExtension = $file->extension();

		foreach ($fileExtensions as $value) {
			if ($value === $checkedExtension) {
				$result = true;
				break;
			}
		}

		return $result;
	}
}
