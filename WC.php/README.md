# Coding Challenge - WC unix tool 

### [WC Tool](https://codingchallenges.fyi/challenges/challenge-wc)

**Challenge**: Build Your Own wc Tool (Unix word count)

- **Language**: PHP 8.5
- **Date Completed**: December 20, 2025
- **Description**:
  > Reproduce the Unix `wc` command to count the number of lines, \n 
  > words, bytes, and characters in a file or text stream.

I wrote two versions of this challenge:
- **OOP version**
- **Programmatic version**

**Usage**:
There are 2 ways to use this challenge:
- using on premise PHP interpreter
- using docker-compose:
  > docker-compose up -d \n
  > docker exec -it ccwc bash to enter the container \n
  > cd src to enter the challenge directory

**Features**:
- ✅ Count lines (`-l`, `--lines`)
- ✅ Count words (`-w`, `--words`)
- ✅ Count bytes (`-c`, `--bytes`)
- ✅ Count characters (`-m`, `--chars`)
- ✅ Support for multiple files
- ✅ Read from STDIN
- ✅ Default mode (lines, words, bytes)

**Usage OOP version (ccwc entrypoint)**:
```bash
# Count lines, words, and bytes (default)
./ccwc file.txt

# Count only lines
./ccwc -l file.txt

# Count words and characters
./ccwc -w -m file.txt

# Read from STDIN
cat file.txt | ./ccwc -l

# Multiple files
./ccwc -l file1.txt file2.txt file3.txt
```

**Usage programmatic version (php ccwc.php entrypoint)**:
```bash
# Count lines, words, and bytes (default)
php ccwc.php file.txt

# Count only lines
php ccwc.php -l file.txt

# Count words and characters
php ccwc.php -w -m file.txt

# Read from STDIN
cat file.txt | php ccwc.php -l
```