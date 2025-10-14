<?php

namespace App\Filament\Resources\Places\Tables;

use App\Models\Place;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlacesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('name')
                    ->label(' Tempat')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Str::limit($state, 20))
                    ->sortable(),
                

                TextColumn::make('descriptions')
                    ->label('Deskripsi')
                    ->html()
                    ->formatStateUsing(fn ($state) => Str::limit(strip_tags($state), 50))
                    ->sortable(),
                    
                TextColumn::make('address')
                    ->label(' Alamat')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Str::limit($state, 20))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Dibuat'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make()
                    ->button()
                    ->color('danger') // default abu-abu (tidak merah)
                    ->requiresConfirmation() // pastikan tampil popup konfirmasi
                    ->modalHeading('Konfirmasi Hapus')
                    ->modalDescription('apakah yakin ingin menghapus data ini?')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
