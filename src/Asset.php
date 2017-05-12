<?php namespace Zanozik\Cdnjs;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model{
	protected $fillable = ['type','name','library','current_version','new_version','latest_version','file','version_mask_check','version_mask_autoupdate','testing'];
}