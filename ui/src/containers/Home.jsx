import React, { Component } from 'react';
import Axios from 'axios';

class Home extends Component {
    constructor() {
        super();

        this.state = {
            form: {}
        };

        this.axios = Axios.create({
            baseURL: '/api/',
            timeout: 1000,
        });

        this.handleChange = this.handleChange.bind(this);
        this.handleClick = this.handleClick.bind(this);
        this.handleKeyPress = this.handleKeyPress.bind(this);

    }

    handleKeyPress(event) {
        if (event.key === 'Enter') {
            this.handleAction();
        }
    }

    handleChange(event) {
        let form = this.state.form;
        form[event.target.name] = event.target.value;
        this.setState({form: form});
    }

    handleClick(event) {
        switch (event.target.id) {
            case 'submit':
                this.handleAction();
                break;
            default:
                break;
        }
    }

    handleAction() {
        this.axios.post('job', {url: this.state.form.url})
            .then((response) => {
                alert(response);
            })
            .catch((error) => {
                alert(error);
            })
        ;
    }

    render() {
        return (
            <div>
                <fieldset className="fieldset">
                    <legend>Qrawler service</legend>
                    <p className="minussss">
                        Enter input URL:
                    </p>
                    <input
                        type="text"
                        placeholder="Input URL"
                        name="url"
                        onKeyPress={this.handleKeyPress}
                        onChange={this.handleChange}
                    />
                    <button id="submit" type="submit" onClick={this.handleClick} className="button small">
                        Validate
                    </button>
                </fieldset>
            </div>

        );
    }
}

export default Home;
