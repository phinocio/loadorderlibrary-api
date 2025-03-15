<?php

namespace App\Console\Commands;

use App\Enums\CacheTag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheTagsTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:test-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if cache tags are working correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing cache tags functionality...');

        try {
            // Test if cache tags are supported
            Cache::tags(['test-tag'])->put('test-key', 'test-value', 60);
            $value = Cache::tags(['test-tag'])->get('test-key');

            if ($value === 'test-value') {
                $this->info('✅ Cache tags are working correctly!');

                // Test flushing tags
                Cache::tags(['test-tag'])->flush();
                $value = Cache::tags(['test-tag'])->get('test-key');

                if ($value === null) {
                    $this->info('✅ Cache tag flushing is working correctly!');
                } else {
                    $this->error('❌ Cache tag flushing is NOT working correctly!');
                }

                // Test multiple tags
                Cache::tags(['tag1', 'tag2'])->put('multi-tag-key', 'multi-tag-value', 60);
                $value = Cache::tags(['tag1', 'tag2'])->get('multi-tag-key');

                if ($value === 'multi-tag-value') {
                    $this->info('✅ Multiple cache tags are working correctly!');

                    // Test flushing one of multiple tags
                    Cache::tags(['tag1'])->flush();
                    $value = Cache::tags(['tag1', 'tag2'])->get('multi-tag-key');

                    if ($value === null) {
                        $this->info('✅ Flushing one of multiple tags is working correctly!');
                    } else {
                        $this->error('❌ Flushing one of multiple tags is NOT working correctly!');
                    }
                } else {
                    $this->error('❌ Multiple cache tags are NOT working correctly!');
                }

                // Test with model constants
                Cache::tags([CacheTag::LOAD_ORDERS->value])->put('model-tag-key', 'model-tag-value', 60);
                $value = Cache::tags([CacheTag::LOAD_ORDERS->value])->get('model-tag-key');

                if ($value === 'model-tag-value') {
                    $this->info('✅ Model cache tags are working correctly!');

                    // Test flushing model tags
                    Cache::tags([CacheTag::LOAD_ORDERS->value])->flush();
                    $value = Cache::tags([CacheTag::LOAD_ORDERS->value])->get('model-tag-key');

                    if ($value === null) {
                        $this->info('✅ Flushing model cache tags is working correctly!');
                    } else {
                        $this->error('❌ Flushing model cache tags is NOT working correctly!');
                    }
                } else {
                    $this->error('❌ Model cache tags are NOT working correctly!');
                }

                // Test game cache tags
                Cache::tags([CacheTag::GAMES->value])->put('game-tag-key', 'game-tag-value', 60);
                $value = Cache::tags([CacheTag::GAMES->value])->get('game-tag-key');

                if ($value === 'game-tag-value') {
                    $this->info('✅ Game cache tags are working correctly!');

                    // Test flushing game tags
                    Cache::tags([CacheTag::GAMES->value])->flush();
                    $value = Cache::tags([CacheTag::GAMES->value])->get('game-tag-key');

                    if ($value === null) {
                        $this->info('✅ Flushing game cache tags is working correctly!');
                    } else {
                        $this->error('❌ Flushing game cache tags is NOT working correctly!');
                    }
                } else {
                    $this->error('❌ Game cache tags are NOT working correctly!');
                }

                // Test game item cache tags
                Cache::tags([CacheTag::GAME_ITEM->withSuffix('1')])->put('game-item-tag-key', 'game-item-tag-value', 60);
                $value = Cache::tags([CacheTag::GAME_ITEM->withSuffix('1')])->get('game-item-tag-key');

                if ($value === 'game-item-tag-value') {
                    $this->info('✅ Game item cache tags are working correctly!');

                    // Test flushing game item tags
                    Cache::tags([CacheTag::GAME_ITEM->withSuffix('1')])->flush();
                    $value = Cache::tags([CacheTag::GAME_ITEM->withSuffix('1')])->get('game-item-tag-key');

                    if ($value === null) {
                        $this->info('✅ Flushing game item cache tags is working correctly!');
                    } else {
                        $this->error('❌ Flushing game item cache tags is NOT working correctly!');
                    }
                } else {
                    $this->error('❌ Game item cache tags are NOT working correctly!');
                }
            } else {
                $this->error('❌ Cache tags are NOT working correctly!');
            }
        } catch (\Exception $e) {
            $this->error('❌ Cache tags are NOT supported by your current cache driver!');
            $this->error('Error: ' . $e->getMessage());
            $this->info('Current cache driver: ' . config('cache.default'));
            $this->info('To use cache tags, you need to use a cache driver that supports them, such as Redis or Memcached.');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
