<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                FileUpload::make('gambar')
                    ->disk('public_folder')
                    ->directory(fn ($record) => $record?->id 
                        ? "media/category/{$record->id}" 
                        : "media/category/temp"
                    )
                    ->getUploadedFileNameForStorageUsing(function ($file, $record) {
                        $ext = $file->getClientOriginalExtension();
                        $datetime = now()->format('Ymd_His');
                        $id = $record?->id ?? 'new'; // fallback kalau belum ada id
                        return "category_{$datetime}_{$id}.{$ext}";
                    })
                    ->visibility('public')
                    ->preserveFilenames(false) // biar selalu generate nama sesuai fungsi di atas
                    ->deleteUploadedFileUsing(fn ($file) => Storage::disk('public_folder')->delete($file))
            ]);
    }
}
