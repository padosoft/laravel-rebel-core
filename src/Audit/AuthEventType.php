<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

/**
 * Tipi di evento di audit più comuni (comodità: il tipo in AuditEvent è una stringa
 * libera, così i bridge possono aggiungere i propri, es. 'fortify.login.succeeded').
 */
enum AuthEventType: string
{
    case EmailOtpRequested = 'email_otp.requested';
    case EmailOtpSent = 'email_otp.sent';
    case EmailOtpVerified = 'email_otp.verified';
    case EmailOtpFailed = 'email_otp.failed';
    case StepUpRequired = 'step_up.required';
    case StepUpVerified = 'step_up.verified';
    case StepUpFailed = 'step_up.failed';
    case LoginSucceeded = 'login.succeeded';
    case LoginFailed = 'login.failed';
    case Logout = 'logout';
    case RecoveryRequested = 'recovery.requested';
    case RecoveryCompleted = 'recovery.completed';
    case RiskAnomalyDetected = 'risk.anomaly.detected';
}
