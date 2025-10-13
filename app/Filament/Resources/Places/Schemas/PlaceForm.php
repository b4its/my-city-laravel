<?php

namespace App\Filament\Resources\Places\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class PlaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                
        
                RichEditor::make('descriptions')   // built-in, TipTap
                    ->label('Deskripsi')
                    ->columnSpanFull()
                    ->required(),

                Select::make('idCategory')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->required(),


                FileUpload::make('gambar')
                    ->disk('public_folder')
                    ->directory(fn ($record) => $record?->id 
                        ? "media/place/{$record->id}" 
                        : "media/place/temp"
                    )
                    ->getUploadedFileNameForStorageUsing(function ($file, $record) {
                        $ext = $file->getClientOriginalExtension();
                        $datetime = now()->format('Ymd_His');
                        $id = $record?->id ?? 'new'; // fallback kalau belum ada id
                        return "place_{$datetime}_{$id}.{$ext}";
                    })
                    ->visibility('public')
                    ->preserveFilenames(false) // biar selalu generate nama sesuai fungsi di atas
                    ->deleteUploadedFileUsing(fn ($file) => Storage::disk('public_folder')->delete($file))
            ]);
    }
}
