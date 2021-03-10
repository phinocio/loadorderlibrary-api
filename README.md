# Load Order Library
Load Order Library is a tool mostly intended to help in debugging/supporting Mod Organizer lists for Bethesda games. It's meant to serve as an alternative/replacement for [Modwatch](https://modwat.ch/). The premiere feature being a list comparison tool. 

I have a Patreon I guess. Don't feel the need to contribute or anything, I have no intention of doing perks/goals (should things take off and people want me to, I probably will). If anything, the only thing I care about is covering the costs of the site/server, which atm are like CA$14/yr for the domain, and ~CA$8/mn for Digital Ocean hosting (though I host a few things on it). See EXPENSES.md for details.

https://www.patreon.com/phinocio

# Table Of Contents

<!-- TOC -->

- [Goal](#goal)
- [Features](#features)
- [Planned Features](#planned-features)
- [Privacy](#privacy)
- [Support/Issues](#supportissues)
- [Thanks](#thanks)

<!-- /TOC -->

# Goal

The goal of Load Order Library is to make the life of people doing support for mod lists easier. Often times we ask for an upload to [Modwatch](https://modwat.ch/) and then manually go through the files to determine if a mod is missing, or a mod is added. Whether the ini settings are correct, etc. By using the comparison tool, you get a quick view at a glance at what files are missing/added, and what contents of those files are also missing/added.

![Image of comparison tool](https://cdn.discordapp.com/attachments/577715234752430082/811766182856097802/unknown.png)

# Features

Load Order Library has a slew of features.

- No registration required. Lists can be uploaded anonymously. You do need and account to delete lists, however. Anonymous ones can't be deleted.
- Private lists. Only people with the link can view them.
- For users that decide to register, you can have as many lists as you want - modwatch only ever lets you have 1.
- Comparison tool.
- Completely free. No Ads.

More planned and coming soon!

# Planned Features 

See [TODO.md](https://github.com/phinocio/loadorderlibrary/blob/master/TODO.md).

# Privacy

Load Order Library uses no analytic services at all. Some 3rd party tools are used, but no external requests are made, they are served with the page itself. Files uploaded currently are on the server forever, however one of the next things I'm doing is implementing a way for files to be deleted if a list that contains them is the only one that does and it is deleted (the same files are shared across lists to save on disk space).

Data provided is entirely for the use of the site, and does not leave the server except in the case of database backups. Passwords are hashed and never stored in plain text. The server is hosted via Digital Ocean and located in Toronto, Canada.

# Support/Issues

If you find any issues or have any questions, please make an issue on this repository or find help on the [Discord server](https://discord.gg/K3KnEgrQE4).

# Changelog

See [CHANGELOG.md](https://github.com/phinocio/loadorderlibrary/blob/master/CHANGELOG.md).

# Thanks

Thanks to RingComics for helping me test.

Thaks to Gatonegro for the icons. 