var uk_stats = {
'E': {name:'England', rate:[1, 18, 23, 28, 33]},
'S': {name:'Scotland', rate:[27.1, 28.2, 6, 15, 34]},
'W': {name:'Wales', rate:[25.6, 25.8, 22.9, 21.1, 21.2]},
'NI': {name:'Northern Ireland', rate:[16, 29.3, 1, 28, 26.1]}
};

uk_stats.maxLevel = 30;
uk_stats.minLevel = 1;
uk_stats.levelIdx = function(level) { return level-1; }
