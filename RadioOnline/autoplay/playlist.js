import fs from 'fs';
import path from 'path';
import { spawn } from 'child_process';
import * as mm from 'music-metadata';

const MUSIC_DIR = './musics'; // Directory with music files
const RTMP_URL = 'icecast://source:source@stream.php-lib.ir/live.mp3'; // Change to your RTMP URL

// Get a list of audio files from the directory
function getMusicFiles(dir) {
    return fs.readdirSync(dir)
        .filter(file => file.match(/\.(mp3|wav|ogg|flac)$/i))
        .map(file => path.join(dir, file));
}

// Shuffle the playlist randomly
function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

// Extract metadata from an audio file
async function getMetadata(filePath) {
    try {
        const metadata = await mm.parseFile(filePath);
        return {
            title: metadata.common.title || path.basename(filePath, path.extname(filePath)),
            artist: metadata.common.artist || 'Unknown Artist',
            album: metadata.common.album || 'Unknown Album',
            duration: metadata.format.duration || 'Unknown Duration'
        };
    } catch (error) {
        console.error(`Error reading metadata for ${filePath}:`, error.message);
        return {
            title: path.basename(filePath, path.extname(filePath)),
            artist: 'Unknown Artist',
            album: 'Unknown Album',
            duration: 'Unknown Duration'
        };
    }
}

function updateIcecastMetadata(metadata) {
    const url = `http://stream.php-lib.ir/admin/metadata?mount=/live.mp3&mode=updinfo&song=${encodeURIComponent(JSON.stringify({ title: metadata.title, artist: metadata.artist }))}`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': 'Basic ' + Buffer.from('source:source').toString('base64') // Icecast admin credentials
        }
    }).then(res => console.log('Metadata updated:', metadata.title))
    .catch(err => console.error('Failed to update metadata:', err));
}

// Function to play and stream music
async function streamMusic() {
    let playlist = shuffleArray(getMusicFiles(MUSIC_DIR));

    if (playlist.length === 0) {
        console.error('No music files found in directory:', MUSIC_DIR);
        process.exit(1);
    }

    let index = 0;

    async function playNext() {
        if (index >= playlist.length) {
            index = 0; // Restart playlist loop
            playlist = shuffleArray(playlist); // Reshuffle on loop
        }

        const currentTrack = playlist[index];
        console.log(`Streaming: ${currentTrack}`);
        
        const metadata = await getMetadata(currentTrack);

		//updateIcecastMetadata(metadata);

        const ffmpegProcess = spawn('ffmpeg', [
            '-re',               // Read input at native speed
            '-i', '/etc/icecast2/' + currentTrack,  // Input file
			'-vn', 
            '-acodec', 'libvorbis',    // Convert to AAC (modify if needed)
            '-b:a', '128k',      // Audio bitrate
			'-content_type', 'audio/ogg',
			'-metadata', 'title=' + JSON.stringify({ title: metadata.title, artist: metadata.artist }).toString(),
            '-f', 'ogg',         // Format for RTMP streaming
            RTMP_URL             // RTMP Output URL
        ]);

        ffmpegProcess.stderr.on('data', (data) => {
            console.error(`${data.toString()}`);
        });

        ffmpegProcess.on('exit', (code) => {
            console.log(`Finished streaming: ${currentTrack}`);
            index++;
            playNext(); // Move to next song
        });
    }

    playNext(); // Start streaming
}

// Start the stream
streamMusic();
