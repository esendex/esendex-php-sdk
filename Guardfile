# A sample Guardfile
# More info at https://github.com/guard/guard#readme

guard 'phpunit2', :cli => '--colors', :tests_path => 'test', :keep_failed => true, :all_after_pass => true, :command => './vendor/bin/phpunit' do
  watch(%r{^test/.+Test\.php$})
  watch(%r{^src/(.+)\.php$}) { |m| "test/#{m[1]}Test.php" }
end
