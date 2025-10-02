# Auto Switch Pause/Resume Logic

## Overview

Auto switch sekarang memiliki logic pause dan resume otomatis ketika user melakukan interaksi dengan map. Ini memberikan UX yang lebih baik karena auto switch tidak berhenti sepenuhnya, melainkan pause sementara dan otomatis resume.

## Flow Logic

### 1. **Normal Flow (Auto Switch Running)**
```
User mengaktifkan auto switch → Auto switch berjalan normal → User tidak interaksi → Auto switch terus berjalan
```

### 2. **User Interaction Flow**
```
Auto switch berjalan → User drag/zoom map → Auto switch pause → Timer 5 detik → Auto switch resume otomatis
```

### 3. **Manual Stop Flow**
```
Auto switch berjalan → User klik toggle → Auto switch stop sepenuhnya → User klik toggle lagi → Auto switch start
```

## State Management

### State Baru yang Ditambahkan:
- `isPaused`: Boolean untuk menunjukkan apakah auto switch sedang pause
- `pauseTimer`: Timer untuk auto resume
- `autoResumeDelay`: Delay untuk auto resume (default: 5000ms)

### Actions Baru:
- `SET_PAUSED`: Set status pause
- `SET_PAUSE_TIMER`: Set timer untuk auto resume

## Functions yang Ditambahkan

### 1. **pauseAutoSwitch()**
- Menghentikan main interval tapi keep fetch interval
- Set timer untuk auto resume setelah 5 detik
- Dispatch event `autoSwitchPaused`
- Set state `isPaused = true`

### 2. **resumeAutoSwitch()**
- Clear pause timer
- Immediately switch ke current station
- Restart main interval
- Dispatch event `autoSwitchResumed`
- Set state `isPaused = false`

### 3. **stopAutoSwitch()** (Updated)
- Stop sepenuhnya (beda dengan pause)
- Clear semua timer dan interval
- Set state `isPlaying = false`

## Event Handling

### User Interaction Events:
- `userInteraction`: Triggered ketika user drag/zoom/click map
- Handler: `pauseAutoSwitch()` (bukan stop)

### Auto Resume Events:
- `autoSwitchPaused`: Dispatched ketika pause
- `autoSwitchResumed`: Dispatched ketika resume

## UI Indicators

### Status Indicators:
1. **Running**: `isPlaying = true, isPaused = false`
   - Green dot dengan "Running (Tick: X)"
   
2. **Paused**: `isPlaying = true, isPaused = true`
   - Yellow dot dengan "Paused (Auto Resume in 5s)"
   
3. **Stopped**: `isPlaying = false`
   - Red dot dengan "Stopped by User"

### Toggle Button States:
- **Not Playing**: "Start Auto Switch"
- **Playing**: "Stop Auto Switch (will pause on user interaction)"
- **Paused**: "Resume Auto Switch (currently paused due to user interaction)"

## Configuration

### Auto Resume Delay:
```javascript
const autoResumeDelay = 5000; // 5 detik
```

### Custom Delay:
```javascript
<AutoSwitchToggleImproved 
  autoResumeDelay={3000} // 3 detik
/>
```

## Benefits

✅ **Better UX**: User tidak kehilangan auto switch ketika interaksi dengan map
✅ **Automatic Resume**: Tidak perlu manual restart
✅ **Visual Feedback**: User tahu status pause/resume
✅ **Smart Timing**: Timer dihitung ulang setiap user interaction
✅ **No Data Loss**: Current station tetap dipertahankan

## Testing Scenarios

### Test 1: Normal Pause/Resume
1. Start auto switch
2. Drag map → Should pause dengan yellow indicator
3. Wait 5 seconds → Should resume otomatis

### Test 2: Multiple Interactions
1. Start auto switch
2. Drag map → Pause
3. Drag map lagi sebelum 5 detik → Timer reset
4. Wait 5 seconds → Should resume

### Test 3: Manual Stop
1. Start auto switch
2. Click toggle → Should stop completely
3. Click toggle → Should start fresh

### Test 4: Manual Resume
1. Start auto switch
2. Drag map → Pause
3. Click toggle → Should resume immediately

## Implementation Notes

- **Fetch interval tetap berjalan** selama pause untuk update data
- **Main interval dihentikan** selama pause
- **Timer di-clear** ketika user interaction baru
- **Immediate switch** ketika resume untuk visual feedback
- **Event dispatching** untuk debugging dan monitoring

