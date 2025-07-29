<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Subjects';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul Subjek')
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required(),
                Select::make('role')
                    ->options([
                        'guest' => 'guest',
                        'star_seller' => 'Star Seller',
                        'mitra_usaha' => 'Mitra Usaha',
                    ])
                    ->label('Untuk Role')
                    ->required(),
                TextInput::make('thumbnail')
                    ->label('Thumbnail URL')
                    ->required(),
                TextInput::make('jumlah_video')
                    ->label('Jumlah Video')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                TextColumn::make('title')->searchable()->label('Judul'),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'star_seller' => 'warning',
                        'mitra_usaha' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('description')->limit(50)->label('Deskripsi'),
                TextColumn::make('jumlah_video') 
                    ->label('Jumlah Video')
                    ->sortable(),
                ImageColumn::make('thumbnail')->label('Thumbnail')->square()->width(100)->height(50),
                TextColumn::make('created_at')->dateTime()->label('Dibuat'),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
