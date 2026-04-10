/**
 * Pixovia Live Stream Database
 * This file contains the primary listing data for the site.
 * For 35,000+ streams, you would typically fetch this from an API,
 * but this structure shows how to manage a large local array.
 */

const STREAMS_DATA = [
    {
        id: 1,
        title: "Al Jazeera Live",
        desc: "Breaking news from around the world, featuring live reports and in-depth analysis of current events.",
        thumb: "https://www.webcamtaxi.com/images/template/thumbs/al-jezeera.jpg",
        category: "News",
        viewers: 15400,
        platform: "Pixovia Player",
        url: "https://pixovia.pages.dev/player/?url=https://live-hls-web-aje-gcp.thehlive.com/AJE/index.m3u8"
    },
    {
        id: 2,
        title: "Vividh Bharti (विविध भारती) online",
        desc: "The original radio. Calm your mind with the best chill beats available 24/7.",
        thumb: "https://static.mytuner.mobi/media/tvos_radios/238/vividh-bharti.92c90eb1.png",
        category: "Music",
        viewers: 42000,
        platform: "Pixovia Player",
        url: "https://pixovia.pages.dev/player/?url=https://air.pc.cdn.bitgravity.com/air/live/pbaudio001/playlist.m3u8"
    },
    {
        id: 3,
        title: "LIVE Playing - Free Fire MAX",
        desc: "Watch the world's best players compete in the ultimate tactical shooter tournament.",
        thumb: "https://images.rooter.gg/rooter-ugc-image/thumbnail/prod-thumbnails-2073615190-1772463885054/hdpi.webp",
        category: "Gaming",
        viewers: 89000,
        platform: "Pixovia Player",
        url: "https://pixovia.pages.dev/player/?url=https://streams.rooter.gg/hls/5d76b7f6eb0778b08155f5bd574c5ea5/high.m3u8"
    },
    {
        id: 4,
        title: "1TV (720p)",
        desc: "Learn how to make restaurant-quality Italian pasta from scratch in this interactive live session.",
        thumb: "https://i.imgur.com/FSkYLPK.png",
        category: "Entertainment",
        viewers: 3200,
        platform: "Pixovia Player",
        url: "https://pixovia.pages.dev/player/?url=https://tv.cdn.xsg.ge/gpb-1tv/index.m3u8"
    },
    {
        id: 6,
        title: "Houston Rockets VS Washington Wizards",
        desc: "Start your day with energy and mindfulness. A live session suitable for all experience levels.",
        thumb: "https://a.espncdn.com/i/teamlogos/nba/500/hou.png",
        category: "Sports",
        viewers: 5600,
        platform: "Pixovia Player",
        url: "https://pixovia.pages.dev/player/?url=https://roseaylrsiu.s3.us-east-1.amazonaws.com/bulls_480p30.m3u8"
    }
    // You can continue adding up to 35,000+ entries here or 
    // automate this list generation from a JSON source.
];

// Exporting for use in the main script
if (typeof module !== 'undefined') {
    module.exports = STREAMS_DATA;
}