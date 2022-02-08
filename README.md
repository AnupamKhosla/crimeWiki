# crimeWiki
## Full stack php cms based project designed and developed solely by Anupam Khosla  

Wikipedea of major criminals, criminal organizations and crime evets.

**Step 1:** Download this git repository. Note your server's databse name, username and password. Open `login.php` in your browser. Use the same database details. Fill in any username and password for cms login.  

**Step 2:** 

- Go to yourdomain/categories.php page first and create a category named `criminals`.  Homepage will show `criminals` category by default.  
- Add a minimum of one post through yourdomain/wikipedea.php or yourdomain/addpost.php. 
- Go to yourdomain/dashboard.php and copy `title` of that post.
- Paste the title into `Crime of the month post`.
- Set the `About The CrimeWiki text`. 

Go to yourdomain and the website will work now.

**Meta:** php will automatically create category named `blog` -- this is mandatory for homepage to show dynamic posts and about us section text. php will make two posts in the blog category, namely `$blog_month_post` and `$blog_about_text`. These two will be used to store about us data and monthly-post data.


