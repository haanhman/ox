import React, {Component} from 'react'
import {View, TouchableOpacity, Text} from 'react-native'
import {connect} from 'react-redux'
// Add Actions - replace 'Your' with whatever your reducer is called :)
// import YourActions from '../Redux/YourRedux'

// Styles
import styles from './Styles/YoutubeVideoScreenStyle'
import YouTube from 'react-native-youtube'

const subtitle = require('../Lib/subtitle.json');

class YoutubeVideoScreen extends Component {

  subtitleKeys = [];
  currentTime = -1;

  constructor(props) {
    super(props);
    this.state = {
      seekToCall: false,
      sub: ''
    }
    this.subtitleKeys = Object.keys(subtitle);
  }

  onChangeState = (e) => {
    const {state} = e;
    if (state == 'buffering' && !this.state.seekToCall) {
      if(this.state.sub == '') {
        this.setState({sub: 'Loading subtitle...'});
      }
      this.youtube.seekTo(137);
      this.setState({seekToCall: true});
    }
    this.setState({status: e.state})
  }


  componentDidMount() {
    setInterval(() => this.checkSubtitle(), 1000);
  }


  checkSubtitle() {
    if (this.state.status != 'playing') {
      return;
    }
    this.youtube.currentTime().then((time) => {
      if (time != this.currentTime && this.subtitleKeys.find(a => a == time)) {
        this.currentTime = time;
        this.setState({sub: subtitle[time].display});
      }
    });
  }

  render() {
    return (
      <View style={styles.container}>
        <YouTube
          apiKey={'AIzaSyDELqju3e3MnUIxCKOc1RAZG7QLCTvhghY'}
          ref={(ref) => this.youtube = ref}
          videoId="_LKpTHa2VoU"   // The YouTube video ID
          play={false}             // control playback of video with true/false
          fullscreen={false}       // control whether the video should play in fullscreen or inline
          loop={false}             // control whether the video should loop when ended

          onReady={e => this.setState({isReady: true})}
          onChangeState={e => this.onChangeState(e)}
          onChangeQuality={e => this.setState({quality: e.quality})}
          onError={e => this.setState({error: e.error})}

          style={{alignSelf: 'stretch', height: 300}}
        />
        <Text style={{padding: 5, fontSize: 20}}>{this.state.sub}</Text>
      </View>
    )
  }
}

const mapStateToProps = (state) => {
  return {}
}

const mapDispatchToProps = (dispatch) => {
  return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(YoutubeVideoScreen)
