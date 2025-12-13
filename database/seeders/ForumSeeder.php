<?php
// database/seeders/ForumSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ForumPost;
use App\Models\ForumAnswer;
use App\Models\ForumLike;
use App\Models\UserFollow;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ForumSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel terlebih dahulu untuk menghindari konflik
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserFollow::truncate();
        ForumLike::truncate();
        ForumAnswer::truncate();
        ForumPost::truncate();

        // Hapus user yang mungkin sudah dibuat oleh seeder sebelumnya
        User::where('email', 'like', '%@example.com')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create users
        $users = User::factory()->count(20)->create([
            'password' => Hash::make('password123'),
        ]);

        // Create specific test user
        $testUser = User::create([
            'name' => 'Anang Ma\'ruf',
            'email' => 'anang@example.com',
            'password' => Hash::make('password123'),
            'avatar' => 'images/pisang.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $users->push($testUser);

        // Categories for posts
        $categories = [
            'Weight Loss',
            'Weight Gain',
            'Maintain Weight',
            'Nutrition',
            'Exercise',
            'Recipes',
            'Mental Health',
            'General'
        ];

        // Create forum posts
        $posts = [];
        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 90));

            $posts[] = ForumPost::create([
                'user_id' => $users->random()->id,
                'title' => $this->generatePostTitle(),
                'content' => $this->generatePostContent(),
                'category' => $categories[array_rand($categories)],
                'views_count' => rand(50, 2554),
                'likes_count' => rand(5, 1880),
                'answers_count' => rand(3, 993),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        // Create answers for posts
        foreach ($posts as $post) {
            $answerCount = rand(3, 20);
            for ($i = 0; $i < $answerCount; $i++) {
                $answerDate = $post->created_at->addDays(rand(0, 30));

                ForumAnswer::create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                    'content' => $this->generateAnswerContent(),
                    'likes_count' => rand(0, 50),
                    'is_best_answer' => $i === 0 ? rand(0, 1) : false,
                    'created_at' => $answerDate,
                    'updated_at' => $answerDate,
                ]);
            }
        }

        // Create likes for posts and answers
        foreach ($users as $user) {
            // Like some posts
            $postsToLike = collect($posts)->random(rand(5, 15));
            foreach ($postsToLike as $post) {
                ForumLike::firstOrCreate([
                    'user_id' => $user->id,
                    'likeable_id' => $post->id,
                    'likeable_type' => ForumPost::class,
                ], [
                    'created_at' => $post->created_at->addDays(rand(1, 10)),
                    'updated_at' => now(),
                ]);
            }

            // Like some answers
            $answers = ForumAnswer::inRandomOrder()->limit(rand(5, 20))->get();
            foreach ($answers as $answer) {
                ForumLike::firstOrCreate([
                    'user_id' => $user->id,
                    'likeable_id' => $answer->id,
                    'likeable_type' => ForumAnswer::class,
                ], [
                    'created_at' => $answer->created_at->addDays(rand(1, 5)),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create follow relationships using firstOrCreate to avoid duplicates
        foreach ($users as $user) {
            $usersToFollow = $users->where('id', '!=', $user->id)->random(rand(3, 8));

            foreach ($usersToFollow as $userToFollow) {
                UserFollow::firstOrCreate([
                    'follower_id' => $user->id,
                    'following_id' => $userToFollow->id,
                ], [
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now(),
                ]);
            }
        }

        // Ensure test user follows and is followed by some users
        $usersToFollow = $users->where('id', '!=', $testUser->id)->random(5);
        foreach ($usersToFollow as $user) {
            UserFollow::firstOrCreate([
                'follower_id' => $testUser->id,
                'following_id' => $user->id,
            ], [
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);

            UserFollow::firstOrCreate([
                'follower_id' => $user->id,
                'following_id' => $testUser->id,
            ], [
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
        }
    }

    private function generatePostTitle()
    {
        $titles = [
            "Bagaimana cara mencegah makan berlebihan tanpa melacak setiap kalori?",
            "Apa camilan tinggi protein terbaik untuk menurunkan berat badan?",
            "Berapa banyak kalori yang harus saya makan untuk mempertahankan berat badan saya saat ini?",
            "Apakah puasa intermiten efektif untuk manajemen berat badan jangka panjang?",
            "Apa saja alternatif sehat untuk keinginan makan gula?",
            "Bagaimana cara tetap termotivasi ketika penurunan berat badan terhenti?",
            "Latihan terbaik untuk membakar kalori di rumah?",
            "Bagaimana cara menghitung kebutuhan kalori berdasarkan tingkat aktivitas?",
            "Apakah aplikasi pelacak kalori akurat?",
            "Apa peran metabolisme dalam manajemen berat badan?",
            "Bagaimana cara mengatasi makan emosional?",
            "Apa minyak goreng paling sehat untuk menghitung kalori?",
            "Berapa banyak air yang harus saya minum untuk menurunkan berat badan?",
            "Bisakah Anda membangun otot saat defisit kalori?",
            "Apa makanan rendah kalori bervolume tinggi terbaik?",
            "Bagaimana cara melacak kalori saat makan di luar?",
            "Apa kebenaran tentang 'kalori masuk, kalori keluar'?",
            "Bagaimana hormon memengaruhi penurunan berat badan dan kebutuhan kalori?",
            "Praktik terbaik untuk persiapan makanan guna kontrol kalori?",
            "Bagaimana cara menembus dataran tinggi penurunan berat badan?"
        ];

        return $titles[array_rand($titles)];
    }

    private function generatePostContent()
    {
        $contents = [
            "Saya telah berjuang dengan masalah ini untuk sementara waktu dan akan sangat menghargai saran dari komunitas. Saya merasa melacak setiap kalori menjadi obsesif dan memakan waktu. Bagaimana Anda mengelola diet sehat tanpa mengatur setiap gigitan secara mikro?",
            "Saya ingin membuat beberapa perubahan pada diet saya tetapi saya tidak yakin harus mulai dari mana. Saya telah mendengar pendapat berbeda dari berbagai sumber dan ingin tahu apa yang berhasil bagi orang sungguhan dalam kehidupan sehari-hari mereka.",
            "Sebagai seseorang yang baru dalam penghitungan kalori, saya penasaran dengan pendekatan terbaik. Saya ingin mengembangkan kebiasaan berkelanjutan daripada perbaikan cepat. Strategi apa yang telah membantu Anda mempertahankan tujuan jangka panjang Anda?",
            "Saya perhatikan berat badan saya cukup berfluktuasi bahkan ketika saya pikir saya makan secara konsisten. Apakah ada faktor di luar penghitungan kalori sederhana yang harus saya pertimbangkan? Bagaimana Anda memperhitungkan variabel-variabel ini?",
            "Dengan begitu banyak informasi yang bertentangan di online, sulit untuk mengetahui apa yang berbasis bukti dan apa yang hanya iseng belaka. Saya ingin mendengar dari orang-orang yang telah berhasil mengelola berat badan mereka melalui manajemen kalori yang berkelanjutan."
        ];

        return $contents[array_rand($contents)];
    }

    private function generateAnswerContent()
    {
        $answers = [
            "Pengalaman saya, kuncinya adalah fokus pada makanan utuh dan mendengarkan sinyal lapar tubuh Anda. Butuh waktu untuk mengembangkan intuisi ini, tetapi itu sepadan untuk kesuksesan jangka panjang.",
            "Saya menemukan bahwa persiapan makan pada hari Minggu membantu saya tetap di jalur sepanjang minggu tanpa harus menghitung setiap kalori. Saya menyiapkan makanan seimbang dengan protein, karbohidrat sehat, dan sayuran.",
            "Jangan lupa pentingnya hidrasi! Terkadang kita salah mengira haus sebagai lapar. Minum segelas air sebelum makan telah membantu saya mengontrol porsi secara alami.",
            "Saya merekomendasikan menggunakan metode piring: setengah sayuran, seperempat protein, seperempat karbohidrat. Pendekatan visual ini menghilangkan kebutuhan akan penghitungan yang tepat sambil memastikan nutrisi seimbang.",
            "Sangat membantu untuk mengingat bahwa tidak semua kalori itu sama. 100 kalori sayuran memengaruhi tubuh Anda secara berbeda dari 100 kalori gula olahan. Fokus pada kualitas makanan di samping kuantitas.",
            "Saya telah sukses dengan praktik makan sadar. Makan perlahan tanpa gangguan memungkinkan saya mengenali kapan saya kenyang dan menghindari makan berlebihan.",
            "Pertimbangkan untuk bekerja sama dengan ahli diet terdaftar jika memungkinkan. Mereka dapat membantu Anda mengembangkan strategi yang dipersonalisasi yang tidak memerlukan pelacakan obsesif.",
            "Saya menyimpan jurnal makanan tanpa menghitung kalori. Cukup menuliskan apa yang saya makan membantu saya tetap bertanggung jawab dan memperhatikan pola dalam kebiasaan makan saya.",
            "Puasa intermiten telah berhasil bagi saya. Dengan membatasi jendela makan saya, saya secara alami mengonsumsi lebih sedikit kalori tanpa pelacakan terperinci.",
            "Ingatlah bahwa konsistensi lebih penting daripada kesempurnaan. Memiliki gambaran kasar tentang kisaran kalori seringkali cukup tanpa perlu melacak setiap kalori."
        ];

        return $answers[array_rand($answers)];
    }
}
