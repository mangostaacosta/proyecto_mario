#Primer intento de ruby en mi vida

require 'rubygems'
require 'open-uri'
require 'json'

puts "Content-Type:text/plain\n"
puts "Content-Length:12\n"
puts "\n"
puts "Hello World! "

ciudad = ARGV.shift

puts ciudad
puts "<br> después de ciudad"
