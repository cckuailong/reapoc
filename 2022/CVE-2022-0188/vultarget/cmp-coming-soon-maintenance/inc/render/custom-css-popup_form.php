<style>
    #subscribe-container-popup.form-container {
    visibility: hidden;
    opacity: 0;
    position: fixed;
    overflow: auto;
    width: 100%;
    top: 0;
    left: 0;
    bottom: 0;
    transform: translate(0);
    color: #696969;
    background: rgba(0, 0, 0, 0.9);
    z-index: 10000;
    transition: all 300ms ease-out;
    pointer-events: none;
}

#subscribe-container-popup.form-container.in-focus {
    visibility: visible;
    opacity: 1;
    pointer-events: initial;
}

#subscribe-container-popup .form-wrapper-popup {
    position: absolute;
    display: flex;
    visibility: visible;
    opacity: 1;
    width: auto;
    max-width: 900px;
    min-height: 500px;
    margin-left: auto;
    margin-right: auto;
    padding: 0;
    left: 0;
    right: 0;
    top: 50%;
    -webkit-transition: -webkit-transform .3s ease-out;
    -o-transition: -o-transform .3s ease-out;
    transition: transform .3s ease-out;
    -webkit-transform: translate(0, -50%);
    -ms-transform: translate(0, -50%);
    -o-transform: translate(0, -50%);
    transform: translate(0, -50%);
    color: white;
    background: rgba(0, 0, 0, 0.92)
}

#subscribe-container-popup .form-wrapper-popup.no-img {
    max-width: 600px;
}

#subscribe-container-popup .subs-img {
    width: 50%;
    height: initial;
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
}

#subscribe-container-popup .form-content {
    padding: 50px;
    width: 50%;
    display: flex;
    flex-direction: column;
    color: black;
    background: white;
}

#subscribe-container-popup .no-img .form-content {
    width: 100%
}

#subscribe-container-popup .in-focus .form-wrapper {
    -webkit-transform: translate(0, -50%);
    -ms-transform: translate(0, -50%);
    -o-transform: translate(0, -50%);
    transform: translate(0, -50%);
}

#subscribe-container-popup .close-popup {
    cursor: pointer;
    font-size: 1.5em;
    position: absolute;
    right: 25px;
    top: 20px;
}

#subscribe-container-popup .form-title {
    text-align: center;
    margin-top: auto;
}

#subscribe-container-popup .cmp-subscribe {
    display: flex;
    flex-direction: column;
    margin-bottom: auto;
}

#subscribe-container-popup .cmp-form-inputs {
    display: block;
    order: 1;
    width: 100%;
    max-width: 100%;
}

#subscribe-form-popup {
    display: flex;
    flex-direction: column;
    margin-bottom: auto;
}

#subscribe-container-popup input {
    padding: 10px 0 10px 10px;
    -webkit-appearance: none;
}


#subscribe-container-popup input[type="email"],
#subscribe-container-popup input[type="text"] {
    color: black;
    border: 1px solid black;
    min-width: 100%;
    padding-left: 0;
    margin: 0;
    margin-bottom: 10px;
    background: transparent;
    text-indent: 10px;
    font-size: 1em;
    border-radius: 0;
    line-height: 1;
    height: 48px;
    box-sizing: border-box;
    text-align: left;
}

#subscribe-container-popup input[type="submit"],
#subscribe-container-popup button[type="submit"],
#subscribe-container-popup button[type="submit"]:hover {
    border: 1px solid black;
    padding: 10px 0;
    min-width: calc(100% + 2px);
    background: black;
    color: white;
    text-transform: uppercase;
    border-radius: 0;
    cursor: pointer;
    font-size: 1em;
    position: relative;
    border-radius: 0;
    line-height: 1;
    height: 48px;
    box-sizing: border-box;
    margin: 0;
}

#subscribe-container-popup.subscribe-button {
    display: inline-block;
    padding: 20px 40px;
    cursor: pointer;
    font-size: 1.1em;
}

#subscribe-container-popup #subscribe-response-popup {
    margin-top: .5em;
    text-align: left;
}

#subscribe-container-popup #gdpr-checkbox-popup {
    -webkit-appearance: checkbox;
    -moz-appearance: checkbox;
    width: initial;
    height: initial;
}

.cmp-form-notes-popup {
    text-align: left;
}

#subscribe-container-popup svg {
    display: none;
}
@media only screen and (max-width: 680px) {
    #subscribe-container-popup .form-wrapper-popup {
        flex-direction: column;
    }

    #subscribe-container-popup .form-content {
        padding: 20px;
        font-size: 14px;
        position: relative;
    }
    #subscribe-container-popup .form-content, #subscribe-container-popup .subs-img{
        width: 100%;
        box-sizing: border-box;
    }
    #subscribe-container-popup .subs-img {
       padding-top: 75%;
    }

    #subscribe-container-popup .form-title {
        font-size: 20px;
    }

    #subscribe-container-popup .close-popup {
        top: 10px;
        right: 10px;
    }
}
</style>