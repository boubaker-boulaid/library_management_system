<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use App\Services\BookCoverStorage;
use Illuminate\Database\Seeder;

/**
 * Seeds ten demo books whose covers ship in public/covers.
 *
 * Covers are stored as bare file names, which BookCoverStorage serves from
 * public/covers. Running the seeder twice updates the same ten books.
 */
class BookSeeder extends Seeder
{
    /**
     * @var list<array{designation: string, auteur: string, editeur: string, annee: string, prix: float, type: string, language: string, categories: list<string>, cover: string, description: string}>
     */
    private const BOOKS = [
        [
            'designation' => 'Is Jazz Dead? (Or Has It Moved to a New Address)',
            'auteur' => 'Stuart Nicholson',
            'editeur' => 'Routledge',
            'annee' => '2005-04-01',
            'prix' => 24.90,
            'type' => 'Text',
            'language' => 'English',
            'categories' => ['Histoire'],
            'cover' => '1767052418_9.jpg',
            'description' => 'A lively look at where jazz went after its golden age, from New York clubs to the new scenes of Europe and beyond.',
        ],
        [
            'designation' => 'Siegfried Sassoon: The Journey from the Trenches',
            'auteur' => 'Jean Moorcroft Wilson',
            'editeur' => 'Routledge',
            'annee' => '2003-09-01',
            'prix' => 32.50,
            'type' => 'Text',
            'language' => 'English',
            'categories' => ['Biographie', 'Histoire'],
            'cover' => '1767099676_1.jpg',
            'description' => 'The second part of the life of the war poet, from the end of the First World War to his death in 1967.',
        ],
        [
            'designation' => 'Installation Art',
            'auteur' => 'Claire Bishop',
            'editeur' => 'Tate Publishing',
            'annee' => '2005-02-01',
            'prix' => 29.00,
            'type' => 'Image',
            'language' => 'English',
            'categories' => ['Histoire'],
            'cover' => '1767099860_4.jpg',
            'description' => 'A critical history of installation art and the ways it asks the viewer to step inside the work.',
        ],
        [
            'designation' => 'Fairy Tales and the Art of Subversion',
            'auteur' => 'Jack Zipes',
            'editeur' => 'Routledge',
            'annee' => '2006-01-01',
            'prix' => 27.50,
            'type' => 'Text',
            'language' => 'English',
            'categories' => ['Fantastique', 'Histoire'],
            'cover' => '1767099940_13.jpg',
            'description' => 'How classic fairy tales shaped children and society, and how writers have turned them against the rules they once taught.',
        ],
        [
            'designation' => 'Marx, Lenin, and the Revolutionary Experience',
            'auteur' => 'Paul Le Blanc',
            'editeur' => 'Routledge',
            'annee' => '2006-03-01',
            'prix' => 34.00,
            'type' => 'Text',
            'language' => 'English',
            'categories' => ['Histoire'],
            'cover' => '1767101202_26.jpg',
            'description' => 'Studies of communism and radicalism in the age of globalization, read through the lives of the movement\'s leading figures.',
        ],
        [
            'designation' => 'The Cinema of Eisenstein',
            'auteur' => 'David Bordwell',
            'editeur' => 'Routledge',
            'annee' => '2005-06-01',
            'prix' => 31.00,
            'type' => 'Text',
            'language' => 'English',
            'categories' => ['Biographie', 'Histoire'],
            'cover' => '1767101306_50.jpg',
            'description' => 'A close study of the films and theories of Sergei Eisenstein, with a new preface by the author.',
        ],
        [
            'designation' => 'Everyday eBay: Culture, Collecting, and Desire',
            'auteur' => 'Ken Hillis, Michael Petit',
            'editeur' => 'Routledge',
            'annee' => '2006-05-01',
            'prix' => 22.00,
            'type' => 'Text',
            'language' => 'English',
            'categories' => ['Histoire'],
            'cover' => '1767112221_5.jpg',
            'description' => 'Essays on how an online auction site changed the way people buy, sell, collect and remember.',
        ],
        [
            'designation' => 'Is Jazz Dead? (Paperback Edition)',
            'auteur' => 'Stuart Nicholson',
            'editeur' => 'Routledge',
            'annee' => '2005-04-01',
            'prix' => 18.90,
            'type' => 'Text',
            'language' => 'English',
            'categories' => ['Histoire'],
            'cover' => '1767114994_9.jpg',
            'description' => 'The paperback edition of Stuart Nicholson\'s essay on the state of jazz at the turn of the century.',
        ],
        [
            'designation' => 'The Winnie-the-Pooh Collection',
            'auteur' => 'A. A. Milne',
            'editeur' => 'Egmont',
            'annee' => '1994-10-01',
            'prix' => 19.50,
            'type' => 'Roman',
            'language' => 'English',
            'categories' => ['Classique', 'Fiction'],
            'cover' => '1767115049_42.jpg',
            'description' => 'The stories of Winnie-the-Pooh and his friends in the Hundred Acre Wood, with illustrations by E. H. Shepard.',
        ],
        [
            'designation' => 'An Expotition to the North Pole',
            'auteur' => 'A. A. Milne',
            'editeur' => 'Egmont',
            'annee' => '1998-03-01',
            'prix' => 9.90,
            'type' => 'Comic',
            'language' => 'English',
            'categories' => ['Classique', 'Fiction'],
            'cover' => 'hh.jpg',
            'description' => 'Christopher Robin leads Pooh and his friends on an expedition to find the North Pole. Illustrated by Ernest H. Shepard.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::BOOKS as $data) {
            /** @var Book $book */
            $book = Book::updateOrCreate(
                ['designation' => $data['designation']],
                [
                    'auteur' => $data['auteur'],
                    'editeur' => $data['editeur'],
                    'annee' => $data['annee'],
                    'prix' => $data['prix'],
                    'description' => $data['description'],
                    'cover' => $this->cover($data['cover']),
                    'type_id' => Type::firstOrCreate(['name' => $data['type']])->id,
                    'language_id' => Language::firstOrCreate(['name' => $data['language']])->id,
                ],
            );

            $book->categories()->sync(
                collect($data['categories'])
                    ->map(fn (string $name) => Category::firstOrCreate(['name' => $name])->id)
                    ->all()
            );
        }
    }

    /**
     * Use the cover when the file exists, otherwise the placeholder.
     */
    private function cover(string $file): string
    {
        return is_file(public_path(BookCoverStorage::DIRECTORY.'/'.$file))
            ? $file
            : BookCoverStorage::PLACEHOLDER;
    }
}
