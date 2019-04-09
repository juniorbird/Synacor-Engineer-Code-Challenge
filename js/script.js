// "use strict";
import { Constants } from "./constants.js";

window.onload = function() {
    main();
};

const main = async () => {
    let ip, weatherData;
    // We use async/await or can use chain of promises
    try {
        ip = await getData('ip');
        console.log(ip);
    } catch (e) {
        console.log(`Can't load coordinates`);
        throw e;
    }

    try {
        weatherData = await getData(`weather/${ip.latitude},${ip.longitude}`);
        console.log(weatherData);
    } catch (e) {
        console.log(`Can't load weather`);
        throw e;
    }

    const icon = document.querySelector('#image');
    icon.src = `http://openweathermap.org/img/w/${weatherData.weather[0].icon}.png`;

    // It's not safety to use innerHtml will use textContent
    const city = document.querySelector('#city');
    city.textContent = weatherData.name;

    // Not sure what measurements
    const temp = document.querySelector('#temp');
    temp.textContent = (weatherData.main.temp | 0) + temp.textContent;

    const conditions = document.querySelector('#conditions');
    conditions.textContent = weatherData.weather[0].description;
};

const getData = async (address) => {
    return await ( await fetch(Constants.API + address)).json();
};
