
# GDPR Compliance Project

For European users, this GDPR project and the GDPR branch aim to review and ensure AtomJump is compliant with the new EU privacy 
laws coming into effect tomorrow (25 May 2018). We have only recently become aware of the requirements, and are now 
exploring what we need to do, if anything, to the AtomJump Messaging open source project and AtomJump.com.

A preliminary look has suggested that anything traceable to a precise name of a person should be obscured within the database.

# Areas for improvement

* Dual-auth is an area that we could develop in future.
* Cookie notification - check the rules for this.


# User identification

Users can choose their own non-unique, unencrypted name, that gets stored per message. A given message
is associated with a single user table entry id.


# Encrypted email addresses

While the email addresses used are not shown publicly, and we tell users this when they enter them,
the question is whether we are required to encrypt them on the stored user table?

We use unencrypted email addresses, and apparently this is not necessarily required to be encrypted:
https://security.stackexchange.com/questions/184519/how-to-handle-emails-as-usernames-under-gdpr

On a preliminary attempt at encrypting the email field via md5(), there were challenges - when sending emails
to other users, you need to have a quick way to decrypt them. If they are easily decryptable, of course, it would
be relatively easy for a hacker to do the same on an extracted table.


# AtomJump.com hosting

On our atomjump.com hosting environment, we split up the database so that different servers have a different
user-table. This limits the damage if one server is compromised, because only the email addresses from that single server
will be visible. 

