EgyptFOSS
====

Development Guide
=
***************************************************************************
### 1. Create Feature Branch
A new branch should be created for each feature, It should be merged after finishing the story and running all feature's tests successfully
    
Branch name convention should be 
> Feature-[Jira-Story/Task-Id]-[Descriptive-name]

Example
> Feature-EGYFOSS-2-Registeration

### 2. Commit the database diff as a sql batch 
sql batch should be located in database/data folder and created using the fossdb.sh command, the following is how to do it:
* Generate database diff using the following command (This will genearte changelog.xml which contains database structure modifications and a sql file with the passed name contains sql data batches)
    
    > fossdb.sh -d  authorname-batchname

> Note: It's better to run "fossdb.sh -t" to test your batches before commit

> Note: Don't use database name in sql batches

### 3. Update database after each pull
* Run 

    > fossdb.sh -u

> Note: It's better to add it to post-merge hook
