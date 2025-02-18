<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDatabase extends BaseModel
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected static function booted()
    {
        static::deleting(function ($service) {
            $service->persistentStorages()->delete();
            $service->fileStorages()->delete();
        });
    }
    public function isLogDrainEnabled()
    {
        return data_get($this, 'is_log_drain_enabled', false);
    }
    public function isGzipEnabled()
    {
        return true;
    }
    public function type()
    {
        return 'service';
    }
    public function serviceType()
    {
        return null;
    }
    public function databaseType()
    {
        $image = str($this->image)->before(':');
        if ($image->value() === 'postgres') {
            $image = 'postgresql';
        }
        return "standalone-$image";
    }
    public function getServiceDatabaseUrl()
    {
        $port = $this->public_port;
        $realIp = $this->service->server->ip;
        if ($this->service->server->isLocalhost() || isDev()) {
            $realIp = base_ip();
        }
        $url = "{$realIp}:{$port}";
        return $url;
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function persistentStorages()
    {
        return $this->morphMany(LocalPersistentVolume::class, 'resource');
    }
    public function fileStorages()
    {
        return $this->morphMany(LocalFileVolume::class, 'resource');
    }
    public function getFilesFromServer(bool $isInit = false)
    {
        getFilesystemVolumesFromServer($this, $isInit);
    }
    public function scheduledBackups()
    {
        return $this->morphMany(ScheduledDatabaseBackup::class, 'database');
    }
}
