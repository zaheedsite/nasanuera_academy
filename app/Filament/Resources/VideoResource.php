<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource\RelationManagers;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;


class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('subject_id')
                    ->relationship('subject', 'title')
                    ->required(),
                TextInput::make('title')->required(),
                Textarea::make('description')->required(),
                TextInput::make('video_url')->label('Video URL')->required(),
                TextInput::make('duration')
                    ->label('Durasi (Contoh: 5 Menit 30 Detik)')
                    ->required(),
                FileUpload::make('thumbnail')
                    ->label('Thumbnail')
                    ->directory('thumbnails') // akan disimpan di storage/app/public/thumbnails
                    ->disk('public') // ⬅️ Tambahkan ini
                    ->visibility('public') // ⬅️ Tambahkan juga ini
                    ->image()
                    ->imagePreviewHeight('100')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Judul Video'),

                Tables\Columns\TextColumn::make('subject.title')
                    ->label('Subjek')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label('Deskripsi'),

                Tables\Columns\TextColumn::make('video_url')
                    ->label('Link Video')
                    ->limit(30)
                    ->url(fn($record) => $record->video_url, true),

                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('public') // karena file ada di storage/app/public
                    ->height(50)
                    ->width(100)
                    ->square(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
